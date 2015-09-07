<?php
// /src/MH4Editor/MH4EditorBundle/Entity/TalismanGenerated.php
namespace MH4Editor\MH4EditorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="talismans_generated")
 */
class TalismanGenerated
{
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="talismansGenerated")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="Talisman")
     * @ORM\JoinColumn(name="talisman_id", referencedColumnName="id")
     **/
    private $talisman;

    /**
     * @ORM\OneToOne(targetEntity="Ability")
     * @ORM\JoinColumn(name="ability1_id", referencedColumnName="id")
     **/
    private $ability1;

    /**
     * @ORM\Column(type="integer",name="ab1_amount")
     */
    private $ability1Amount;

    /**
     * @ORM\OneToOne(targetEntity="Ability")
     * @ORM\JoinColumn(name="ability2_id", referencedColumnName="id")
     **/
    private $ability2;

    /**
     * @ORM\Column(type="integer",name="ab2_amount")
     */
    private $ability2Amount;

    /**
     * @ORM\Column(type="integer",name="slots")
     */
    private $slots;

    /**
     * @ORM\Column(type="datetime",name="creation_date")
     */
    private $creationDate;



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
     * Set ability1Amount
     *
     * @param integer $ability1Amount
     * @return TalismanGenerated
     */
    public function setAbility1Amount($ability1Amount)
    {
        $this->ability1Amount = $ability1Amount;

        return $this;
    }

    /**
     * Get ability1Amount
     *
     * @return integer 
     */
    public function getAbility1Amount()
    {
        return $this->ability1Amount;
    }

    /**
     * Set ability2Amount
     *
     * @param integer $ability2Amount
     * @return TalismanGenerated
     */
    public function setAbility2Amount($ability2Amount)
    {
        $this->ability2Amount = $ability2Amount;

        return $this;
    }

    /**
     * Get ability2Amount
     *
     * @return integer 
     */
    public function getAbility2Amount()
    {
        return $this->ability2Amount;
    }

    /**
     * Set slots
     *
     * @param integer $slots
     * @return TalismanGenerated
     */
    public function setSlots($slots)
    {
        $this->slots = $slots;

        return $this;
    }

    /**
     * Get slots
     *
     * @return integer 
     */
    public function getSlots()
    {
        return $this->slots;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return TalismanGenerated
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set user
     *
     * @param \MH4Editor\MH4EditorBundle\Entity\User $user
     * @return TalismanGenerated
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

    /**
     * Set talisman
     *
     * @param \MH4Editor\MH4EditorBundle\Entity\Talisman $talisman
     * @return TalismanGenerated
     */
    public function setTalisman(\MH4Editor\MH4EditorBundle\Entity\Talisman $talisman = null)
    {
        $this->talisman = $talisman;

        return $this;
    }

    /**
     * Get talisman
     *
     * @return \MH4Editor\MH4EditorBundle\Entity\Talisman 
     */
    public function getTalisman()
    {
        return $this->talisman;
    }

    /**
     * Set ability1
     *
     * @param \MH4Editor\MH4EditorBundle\Entity\Ability $ability1
     * @return TalismanGenerated
     */
    public function setAbility1(\MH4Editor\MH4EditorBundle\Entity\Ability $ability1 = null)
    {
        $this->ability1 = $ability1;

        return $this;
    }

    /**
     * Get ability1
     *
     * @return \MH4Editor\MH4EditorBundle\Entity\Ability 
     */
    public function getAbility1()
    {
        return $this->ability1;
    }

    /**
     * Set ability2
     *
     * @param \MH4Editor\MH4EditorBundle\Entity\Ability $ability2
     * @return TalismanGenerated
     */
    public function setAbility2(\MH4Editor\MH4EditorBundle\Entity\Ability $ability2 = null)
    {
        $this->ability2 = $ability2;

        return $this;
    }

    /**
     * Get ability2
     *
     * @return \MH4Editor\MH4EditorBundle\Entity\Ability 
     */
    public function getAbility2()
    {
        return $this->ability2;
    }
}
