<?php
// /src/MH4Editor/MH4EditorBundle/Entity/User.php
namespace MH4Editor\MH4EditorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="MH4Editor\MH4EditorBundle\Entity\UserRepository")
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
    const ITEM_QUOTA_BASE = 500;
    const TALISMAN_QUOTA_BASE = 10;
    
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

    /**
     * @ORM\Column(name="register_date",type="datetime")
     */
    private $registerDate;

    /**
     * @ORM\Column(name="last_login",type="datetime")
     */
    private $lastLogin;

    /**
     * @ORM\Column(name="last_download_request",type="datetime")
     */
    private $lastDownloadRequest;

    /**
     * @ORM\Column(name="last_ua",type="text")
     */
    private $lastUserAgent;

    /**
     * @ORM\Column(name="last_ip",type="string")
     */
    private $lastIp;


    private $temp;

    /**
     * @ORM\OneToMany(targetEntity="TalismanGenerated", mappedBy="user")
     */
    protected $talismansGenerated;

    /**
     * @ORM\OneToMany(targetEntity="ItemBought", mappedBy="user")
     */
    protected $itemsBought;

    /**
     * @ORM\Column(name="items_quota",type="integer")
     */
    protected $itemsQuota;

    /**
     * @ORM\Column(name="max_items_quota",type="integer")
     */
    protected $maxItemsQuota;

    /**
     * @ORM\Column(name="talismans_quota",type="integer")
     */
    protected $talismansQuota;

    /**
     * @ORM\Column(name="max_talismans_quota",type="integer")
     */
    protected $maxTalismansQuota;

    /**
     * @ORM\Column(name="hunter_name",type="string")
     */
    protected $hunterName;

    /**
     * @ORM\Column(name="hunter_hr",type="integer")
     */
    protected $hunterHr;

    /**
     * @Assert\File(maxSize=81408, mimeTypes = "application/octet-stream")
     */
    private $mh4File;

    public function setMH4FilePath($path){
        $this->mh4save_path = $path;
        return $this;
    }

    public function getMH4FilePath(){
        return  $this->mh4save_path;
    }

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

            //$filename = sha1(uniqid(mt_rand()),true);
            //$this->setMh4savePath($filename.'.'.$this->getMh4File()->guessExtension());
            $this->mh4save_path = "uploads/save_file/".$this->getUsername()."/".$this->getMh4File()->getClientOriginalName();
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

        if(null === $this->getMh4File()){
            return;
        }

        $file = $this->getMh4File()->move(
            $this->getUploadDir(),
            $this->getMh4File()->getClientOriginalName()
        );
        
        if(isset($this->temp)){

            //echo "Removing...".$this->getUploadRootDir().'/'.$this->temp;die;
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

    public function getDefaultSavePath(){

        return 'uploads/save_file/'.$this->getUsername();
    }

    public function getUploadDir(){

        return $this->getUploadRootDir().'/uploads/save_file/'.$this->getUsername();
    }

    public function __construct()
    {
        $this->isActive = true;
        $this->talismansGenerated = new ArrayCollection();
        $this->itemsBought = new ArrayCollection();

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


    /**
     * Set registerDate
     *
     * @param \DateTime $registerDate
     * @return User
     */
    public function setRegisterDate($registerDate){

        $this->registerDate = $registerDate;
        return $this;
    }

    /**
     * Get registerDate
     *
     * @return datetime 
     */
    public function getRegisterDate(){

        return $this->registerDate;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     * @return User
     */
    public function setLastLogin($lastLogin){

        $this->lastLogin = $lastLogin;
        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return datetime 
     */
    public function getLastLogin(){

        return $this->lastLogin;
    }

    /**
     * Set lastDownloadRequest
     *
     * @param \DateTime $lastDownloadRequest
     * @return User
     */
    public function setLastDownloadRequest($lastDownloadRequest){

        $this->lastDownloadRequest = $lastDownloadRequest;
        return $this;
    }

    /**
     * Get lastDownloadRequest
     *
     * @return \DateTime
     */
    public function getLastDownloadRequest(){

        return $this->lastDownloadRequest;
    }

    /**
     * Set lastUserAgent
     *
     * @param string $lastUserAgent
     * @return User
     */
    public function setLastUserAgent($lastUserAgent){

        $this->lastUserAgent = $lastUserAgent;
        return $this;
    }

    /**
     * Get lastUserAgent
     *
     * @return string 
     */
    public function getLastUserAgent(){

        return $this->lastUserAgent;
    }

    /**
     * Set lastIp
     *
     * @param string $lastIp
     * @return User
     */
    public function setLastIP($lastIp){

        $this->lastIp = $lastIp;
        return $this;
    }

    /**
     * Get lastIp
     *
     * @return string 
     */
    public function getLastIP(){

        return $this->lastIp;
    }

    /**
     * Set itemsQuota
     *
     * @param integer $itemsQuota
     * @return User
     */
    public function setItemsQuota($itemsQuota){

        $this->itemsQuota = $itemsQuota;
        return $this;
    }

    /**
     * Get itemsQuota
     *
     * @return integer 
     */
    public function getItemsQuota(){

        return $this->itemsQuota;
    }

    /**
     * Set maxItemsQuota
     *
     * @param integer $maxItemsQuota
     * @return User
     */
    public function setMaxItemsQuota($maxItemsQuota){

        $this->maxItemsQuota = $maxItemsQuota;
        return $this;
    }

    /**
     * Get maxItemsQuota
     *
     * @return integer 
     */
    public function getMaxItemsQuota(){

        return $this->maxItemsQuota;
    }

    /**
     * Set talismansQuota
     *
     * @param integer $talismansQuota
     * @return User
     */
    public function setTalismansQuota($talismansQuota){

        $this->talismansQuota = $talismansQuota;
        return $this;
    }

    /**
     * Get talismansQuota
     *
     * @return integer 
     */
    public function getTalismansQuota(){

        return $this->talismansQuota;
    }

    //
    /**
     * Set maxTalismansQuota
     *
     * @param integer $maxTalismansQuota
     * @return User
     */
    public function setMaxTalismansQuota($maxTalismansQuota){

        $this->maxTalismansQuota = $maxTalismansQuota;
        return $this;
    }

    /**
     * Get maxTalismansQuota
     *
     * @return integer 
     */
    public function getMaxTalismansQuota(){

        return $this->maxTalismansQuota;
    }

    /**
     * Set hunterName
     *
     * @param string $hunterName
     * @return User
     */
    public function setHunterName($hunterName){

        $this->hunterName = $hunterName;
        return $this;
    }

    /**
     * Get hunterName
     *
     * @return string 
     */
    public function getHunterName(){

        return $this->hunterName;
    }

    /**
     * Set hunterHr
     *
     * @param integer $hunterHr
     * @return User
     */
    public function setHunterHR($hunterHr){

        $this->hunterHr = $hunterHr;
        return $this;
    }

    /**
     * Get hunterHr
     *
     * @return integer 
     */
    public function getHunterHR(){

        return $this->hunterHr;
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