<?php
// /src/MH4Editor/MH4EditorBundle/Entity/User.php
namespace MH4Editor\MH4EditorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 * @ORM\Table(name="users")
 */
class User implements UserInterface, \Serializable
{

    const USER  = 0;
    const ADMIN = 1;

    const DEFAULT_LOCALE = "en";
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=6)
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=6)
     * @Assert\Regex(pattern="/(?!^[0-9]*$)(?!^[a-zA-Z]*$)^([a-zA-Z0-9])+/",message="Password must contain numeric and alphabetic characters.")
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @Assert\NotBlank
     * @Assert\Email
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    private $mh4save_path;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(name="is_banned", type="boolean")
     */
    private $isBanned;

    /**
     * @ORM\Column(name="role", type="integer",options={"default" = "0"})
     */
    private $role;

    /**
     * @ORM\Column(name="locale", type="string",options={"default" = "en"})
     */
    private $locale;


    private $temp;

    /**
     * @Assert\File(maxSize=81408, mimeTypes = "application/octet-stream")
     */
    private $mh4File;

    public function setMh4File(UploadedFile $file = null){

        $this->mh4File = $file;

        if(isset($this->mh4save_path)){
            $this->temp = $this->mh4save_path;
            $this->mh4save_path = null;
        }else{
            $this->mh4save_path = "uploads/save_file/".$this->getUsername()."/".$file->getClientOriginalName();
            //echo "HOLAA";die; <-- llega UploadedFile OK! Seguir mirando...
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload(){

        if(null !== $this->getMh4File()){

            $filename = sha1(uniqid(mt_rand()),true);
            $this->setMh4savePath($filename.'.'.$this->getMh4File()->guessExtension());
        }
    }

    public function getMh4File(){
        
        return $this->mh4File;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload(){

        if(null === $this->getMh4File())
            return;

        $this->getMh4File()->move(
            $this->getUploadRootDir(),
            $this->getMh4File()->getClientOriginalName()
        );

        if(isset($this->temp)){
            unlink($this->getUploadRootDir().'/'.$this->temp);

            $this->temp = null;
        }

        //$this->setMh4savePath($this->getMh4File()->getClientOriginalName());

        $this->mh4File= null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload(){

        $file = $this->getAbsolutePath();
        if($file)
            unlink($file);

    }

    public function getAbsolutePath(){

        return null === $this->mh4save_path ? null : $this->getUploadRootDir().'/'.$this->mh4save_path;

    }

    public function getWebPath(){

        return null === $this->mh4save_path ? null : $this->mh4save_path;
    }

    public function getUploadRootDir(){

        return __DIR__."/../../../../web";
    }

    public function getUploadDir(){

        return $this->getUploadRootDir().'/uploads/save_file/'.$this->getUsername();
    }

    public function __construct()
    {
        $this->isActive = true;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid(null, true));
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        $r = $this->getRole();
        if($r === self::USER)
            return array('ROLE_USER');
        else if($r === self::ADMIN)
            return array('ROLE_SUPER_ADMIN');
        else
            return array('IS_AUTHENTICATED_ANONYMOUSLY');
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }


    /**
     *@ORM\PrePersist()
     */
    public function encryptPassword(){
        $this->password = password_hash($this->password,PASSWORD_BCRYPT,array("cost" => 12));
    }
    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set mh4save_path
     *
     * @param string $mh4savePath
     * @return User
     */
    public function setMh4savePath($mh4savePath)
    {
        $this->mh4save_path = $mh4savePath;
        echo "1||";
        if(null !== $this->getMh4File()){
            $filename = sha1(uniqid(mt_rand()),true);
            $this->mh4save_path.='/'.$filename.'.'.$this->getMh4File()->guessExtension();
        }

        return $this;
    }

    /**
     * Get mh4save_path
     *
     * @return string 
     */
    public function getMh4savePath()
    {
        return $this->mh4save_path;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isBanned
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsBanned($isBanned)
    {
        $this->isBanned = ($isBanned !== null && $isBanned != "") ? $isBanned : false;

        return $this;
    }

     /**
     * Get isBanned
     *
     * @return boolean 
     */
    public function getIsBanned()
    {
        return $this->isBanned;
    }

    /**
     * Set role
     *
     * @param integer $role
     * @return User
     */
    public function setRole()
    {
        $this->role = 0;

        return $this;
    }

    /**
     * Get role
     *
     * @return integer 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return User
     */
    public function setLocale($locale)
    {
        if($locale!== null && $locale !== "")
            $this->locale = $locale;
        else
            $this->locale = DEFAULT_LOCALE;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string 
     */
    public function getLocale()
    {
        return ($this->locale != null && $this->locale != "") ? $this->locale : DEFAULT_LOCALE;
    }

    public function isAccountNonExpired(){

    }

    public function isAccountNonLocked(){

    }

    public function isCredentialsNonExpired(){

    }

    public function isEnabled(){

        return $this->getIsBanned();

    }


    
}
