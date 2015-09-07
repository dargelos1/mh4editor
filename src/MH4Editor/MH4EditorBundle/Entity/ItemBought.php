<?php
// /src/MH4Editor/MH4EditorBundle/Entity/ItemBought.php
namespace MH4Editor\MH4EditorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="items_bought")
 */
class ItemBought
{
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Item")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     **/
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="itemsBought")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime",name="purchase_date")
     */
    private $purchaseDate;

    /**
     * @ORM\Column(type="integer",name="units")
     */
    private $units;

    /**
     * @ORM\Column(type="integer",name="money_wasted")
     */
    private $moneyWasted;

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
     * Set purchaseDate
     *
     * @param \DateTime $purchaseDate
     * @return ItemBought
     */
    public function setPurchaseDate($purchaseDate)
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    /**
     * Get purchaseDate
     *
     * @return \DateTime 
     */
    public function getPurchaseDate()
    {
        return $this->purchaseDate;
    }

    /**
     * Set units
     *
     * @param integer $units
     * @return ItemBought
     */
    public function setUnits($units)
    {
        $this->units = $units;

        return $this;
    }

    /**
     * Get units
     *
     * @return integer 
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * Set moneyWasted
     *
     * @param integer $moneyWasted
     * @return ItemBought
     */
    public function setMoneyWasted($moneyWasted)
    {
        $this->moneyWasted = $moneyWasted;

        return $this;
    }

    /**
     * Get moneyWasted
     *
     * @return integer 
     */
    public function getMoneyWasted()
    {
        return $this->moneyWasted;
    }

    /**
     * Set item
     *
     * @param \MH4Editor\MH4EditorBundle\Entity\Item $item
     * @return ItemBought
     */
    public function setItem(\MH4Editor\MH4EditorBundle\Entity\Item $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return \MH4Editor\MH4EditorBundle\Entity\Item 
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set user
     *
     * @param \MH4Editor\MH4EditorBundle\Entity\User $user
     * @return ItemBought
     */
    public function setUser(\MH4Editor\MH4EditorBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \MH4Editor\MH4EditorBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
