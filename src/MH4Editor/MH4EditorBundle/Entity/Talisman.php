<?php
// /src/MH4Editor/MH4EditorBundle/Entity/Talisman.php
namespace MH4Editor\MH4EditorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="talismans")
 */
class Talisman
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
     * @ORM\Column(type="integer")
     */
    private $rarity;

    /**
     * @ORM\Column(type="text")
     */
    private $icon;



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
     * @return Talisman
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
     * @return Talisman
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
     * Set rarity
     *
     * @param integer $rarity
     * @return Talisman
     */
    public function setRarity($rarity)
    {
        $this->rarity = $rarity;

        return $this;
    }

    /**
     * Get rarity
     *
     * @return integer 
     */
    public function getRarity()
    {
        return $this->rarity;
    }

    /**
     * Set icon
     *
     * @param string $icon
     * @return Talisman
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string 
     */
    public function getIcon()
    {
        return $this->icon;
    }

    public function getUrlPath(){

        return 'bundles/design/images/icons_items/'.$this->getIcon();
    }
}
