<?php
// /src/MH4Editor/MH4EditorBundle/Entity/Item.php
namespace MH4Editor\MH4EditorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="MH4Editor\MH4EditorBundle\Entity\ItemRepository")
 * @ORM\Table(name="items")
 */
class Item
{

    /* Degrees */
    const RARITY_1  = 1;
    const RARITY_2  = 2;
    const RARITY_3  = 3;
    const RARITY_4  = 4;
    const RARITY_5  = 5;
    const RARITY_6  = 6;
    const RARITY_7  = 7;
    const RARITY_8  = 8;
    const RARITY_9  = 9;
    const RARITY_10 = 10;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $name;

     /**
     * @ORM\Column(type="text",name="name_en")
     */
    private $nameEn;

    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="ItemType")
     * @ORM\JoinColumn(name="type", referencedColumnName="id") <-- Mirar documentacion
     */
    private $type;

    /**
     * @ORM\Column(type="text",name="sub_type")
     */
    private $subType;

    /**
     * @ORM\Column(type="integer")
     */
    private $rarity;

    /**
     * @ORM\Column(type="integer",name="carry_capacity")
     */
    private $carryCapacity;

    /**
     * @ORM\Column(type="integer",name="buy")
     */
    private $buyPrice;

    /**
     * @ORM\Column(type="integer",name="sell")
     */
    private $sellPrice;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

     /**
     * @ORM\Column(type="string", length=255,name="description_en")
     */
    private $descriptionEn;

    /**
     * @ORM\Column(type="integer",name="web_zenis_buy")
     */
    private $zenisWebBuyValue;

    /**
     * @ORM\Column(type="integer",name="web_zenis_sell")
     */
    private $zenisWebSellValue;

    /**
     * @ORM\Column(type="integer",name="web_caravan_points_buy")
     */
    private $caravanWebBuyValue;

    /**
     * @ORM\Column(type="integer",name="web_caravan_points_sell")
     */
    private $caravanWebSellValue;

    /**
     * @ORM\Column(type="text")
     */
    private $icon;

    /**
     * @ORM\Column(type="text",name="canonicalName")
     */
    private $canonicalName;

    /**
     * @ORM\Column(type="integer",name="box_capacity")
     */
    private $boxCapacity;

    /**
     * @ORM\Column(type="integer",name="times_bought")
     */
    private $timesBought;

    /**
     * @ORM\Column(type="boolean",name="locked")
     */
    private $locked;


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
     * @return Item
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
     * Get nameEn
     *
     * @return string 
     */
    public function getEnglishName()
    {
        return $this->nameEn;
    }

    /**
     * Set nameEn
     *
     * @param string $nameEn
     * @return Item
     */
    public function setEnglishName($nameEn)
    {
        $this->nameEn = $nameEn;

        return $this;
    }


    /**
     * Set type
     *
     * @param integer $type
     * @return Item
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set subType
     *
     * @param integer $subType
     * @return Item
     */
    public function setSubType($subType)
    {
        $this->subType = $subType;

        return $this;
    }

    /**
     * Get subType
     *
     * @return integer 
     */
    public function getSubType()
    {
        return $this->subType;
    }

    /**
     * Set rarity
     *
     * @param integer $rarity
     * @return Item
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
     * Set carryCapacity
     *
     * @param integer $carryCapacity
     * @return Item
     */
    public function setCarryCapacity($carryCapacity)
    {
        $this->carryCapacity = $carryCapacity;

        return $this;
    }

    /**
     * Get carryCapacity
     *
     * @return integer 
     */
    public function getCarryCapacity()
    {
        return $this->carryCapacity;
    }

    /**
     * Set buyPrice
     *
     * @param integer $buyPrice
     * @return Item
     */
    public function setBuyPrice($buyPrice)
    {
        $this->buyPrice = $buyPrice;

        return $this;
    }

    /**
     * Get buyPrice
     *
     * @return integer 
     */
    public function getBuyPrice()
    {
        return $this->buyPrice;
    }

    /**
     * Set sellPrice
     *
     * @param integer $sellPrice
     * @return Item
     */
    public function setSellPrice($sellPrice)
    {
        $this->sellPrice = $sellPrice;

        return $this;
    }

    /**
     * Get sellPrice
     *
     * @return integer 
     */
    public function getSellPrice()
    {
        return $this->sellPrice;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Item
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
     * Set descriptionEn
     *
     * @param string $description
     * @return Item
     */
    public function setEnglishDescription($descriptionEn)
    {
        $this->descriptionEn = $descriptionEn;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getEnglishDescription()
    {
        return $this->descriptionEn;
    }

    /**
     * Set zenisWebBuyValue
     *
     * @param integer $zenisWebBuyValue
     * @return Item
     */
    public function setZenisWebBuyValue($zenisWebBuyValue)
    {
        $this->zenisWebBuyValue = $zenisWebBuyValue;

        return $this;
    }

    /**
     * Get zenisWebBuyValue
     *
     * @return integer 
     */
    public function getZenisWebBuyValue()
    {
        return $this->zenisWebBuyValue;
    }

    /**
     * Set zenisWebSellValue
     *
     * @param integer $zenisWebSellValue
     * @return Item
     */
    public function setZenisWebSellValue($zenisWebSellValue)
    {
        $this->zenisWebSellValue = $zenisWebSellValue;

        return $this;
    }

    /**
     * Get zenisWebSellValue
     *
     * @return integer 
     */
    public function getZenisWebSellValue()
    {
        return $this->zenisWebSellValue;
    }

    /**
     * Set caravanWebBuyValue
     *
     * @param integer $caravanWebBuyValue
     * @return Item
     */
    public function setCaravanWebBuyValue($caravanWebBuyValue)
    {
        $this->caravanWebBuyValue = $caravanWebBuyValue;

        return $this;
    }

    /**
     * Get caravanWebBuyValue
     *
     * @return integer 
     */
    public function getCaravanWebBuyValue()
    {
        return $this->caravanWebBuyValue;
    }

    /**
     * Set caravanWebSellValue
     *
     * @param integer $caravanWebSellValue
     * @return Item
     */
    public function setCaravanWebSellValue($caravanWebSellValue)
    {
        $this->caravanWebSellValue = $caravanWebSellValue;

        return $this;
    }

    /**
     * Get caravanWebSellValue
     *
     * @return integer 
     */
    public function getCaravanWebSellValue()
    {
        return $this->caravanWebSellValue;
    }

    /**
     * Set icon
     *
     * @param string $icon
     * @return Item
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

    /**
     * Set canonicalName
     *
     * @param string $canonicalName
     * @return Item
     */
    public function setCanonicalName($canonicalName)
    {
        $this->canonicalName = $canonicalName;

        return $this;
    }

    /**
     * Get canonicalName
     *
     * @return string 
     */
    public function getCanonicalName()
    {
        return $this->canonicalName;
    }

    /**
     * Set canonicalName
     *
     * @param string $canonicalName
     * @return Item
     */
    public function setBoxCapacity($boxCapacity)
    {
        $this->boxCapacity = $boxCapacity;

        return $this;
    }

    /**
     * Get boxCapacity
     *
     * @return integer 
     */
    public function getBoxCapacity()
    {
        return $this->boxCapacity;
    }

    /**
     * Set timesBought
     *
     * @param string $timesBought
     * @return Item
     */
    public function setTimesBought($timesBought)
    {
        $this->timesBought = $timesBought;

        return $this;
    }

    /**
     * Get timesBought
     *
     * @return integer 
     */
    public function getTimesBought()
    {
        return $this->timesBought;
    }

    /**
     * Set locked
     * 
     * @param boolean $locked
     * @return Item 
     */
    public function setIsLocked($locked)
    {
        $this->locked = $locked;
        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean 
     */
    public function getIsLocked()
    {
        return $this->locked;
    }

    /**
     * Set nameEn
     *
     * @param string $nameEn
     * @return Item
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
     * Set descriptionEn
     *
     * @param string $descriptionEn
     * @return Item
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
}
