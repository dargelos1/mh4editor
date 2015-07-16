<?php
// /src/MH4Editor/MH4EditorBundle/Entity/Item.php
namespace MH4Editor\MH4EditorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="item_type")
 */
class ItemType
{
    /* Types */
    const TYPE_TOOL         = 1;
    const TYPE_BOOK         = 2;
    const TYPE_CONSUMABLE   = 3;
    const TYPE_ORE          = 4;
    const TYPE_MEAT         = 5;
    const TYPE_PLANT        = 6;
    const TYPE_BONE         = 7;
    const TYPE_AMMO         = 8;
    const TYPE_COATING      = 9;
    const TYPE_BAIT         = 10;
    const TYPE_WYSTONE      = 11;
    const TYPE_FISH         = 12;
    const TYPE_BUG          = 13;
    const TYPE_NECTAR       = 14;
    const TYPE_OTHER        = 15;
    const TYPE_FLESH        = 16;
    const TYPE_SACFLUID     = 17;
    const TYPE_SUPPLY       = 18;
    const TYPE_ACCOUNT      = 19;
    const TYPE_COINTICKET   = 20;
    const TYPE_COMMODITY    = 21;
    const TYPE_SCRAP        = 22;
    const TYPE_DECORATION   = 23;
    const TYPE_ARMOR        = 24;
    const TYPE_WEAPON       = 25;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $name;



    /**
     * Set id
     *
     * @param integer $id
     * @return ItemType
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set name
     *
     * @param string $name
     * @return ItemType
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
}
