<?php
// /src/MH4Editor/MH4EditorBundle/Entity/AbilityActive.php
namespace MH4Editor\MH4EditorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ability_activated")
 */
class AbilityActive
{
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text",name="name")
     */
    private $name;

     /**
     * @ORM\Column(type="text",name="name_en")
     */
    private $nameEn;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255,name="description_en")
     */
    private $descriptionEn;

    /**
     * @ORM\Column(type="integer")
     */
    private $points;

    /**
     * @ORM\ManyToOne(targetEntity="Ability", inversedBy="abilitiesActive")
     * @ORM\JoinColumn(name="ability_id", referencedColumnName="id")
     */
    private $ability;

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
     * Set name
     *
     * @param string $name
     * @return Ability
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set nameEn
     *
     * @param string $nameEn
     * @return Ability
     */
    public function setNameEn($nameEn)
    {
        $this->nameEn = $nameEn;

        return $this;
    }

    /**
     * Get nameEn
     *
     * @return string 
     */
    public function getNameEn()
    {
        return $this->nameEn;
    }

    /**
     * Get nameEn
     *
     * @return string 
     */
    public function getEnglishName()
    {
        return $this->getNameEn();
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Ability
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getEnglishDescription()
    {
        return $this->description;
    }

    /**
     * Set descriptionEn
     *
     * @param string $descriptionEn
     * @return Ability
     */
    public function setDescriptionEn($descriptionEn)
    {
        $this->descriptionEn = $descriptionEn;

        return $this;
    }

    /**
     * Get descriptionEn
     *
     * @return string 
     */
    public function getDescriptionEn()
    {
        return $this->descriptionEn;
    }

    /**
     * Set points
     *
     * @param integer $points
     * @return AbilityActive
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set ability
     *
     * @param \MH4Editor\MH4EditorBundle\Entity\Ability $ability
     * @return AbilityActive
     */
    public function setAbility(\MH4Editor\MH4EditorBundle\Entity\Ability $ability = null)
    {
        $this->ability = $ability;

        return $this;
    }

    /**
     * Get ability
     *
     * @return \MH4Editor\MH4EditorBundle\Entity\Ability 
     */
    public function getAbility()
    {
        return $this->ability;
    }
}
