<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Office
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int|null
     */
    protected $regularOpeningHour;

    /**
     * @var int|null
     */
    protected $regularClosingHour;

    /**
     * @var Address
     */
    protected $address;

    /**
     * @var User[]|ArrayCollection
     */
    protected $referents;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->address = new Address();
        $this->referents = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->getName();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return int|null
     */
    public function getRegularOpeningHour()
    {
        return $this->regularOpeningHour;
    }

    /**
     * @param int|null $regularOpeningHour
     */
    public function setRegularOpeningHour($regularOpeningHour = null)
    {
        $this->regularOpeningHour = $regularOpeningHour;
    }

    /**
     * @return int|null
     */
    public function getRegularClosingHour()
    {
        return $this->regularClosingHour;
    }

    /**
     * @param int|null $regularClosingHour
     */
    public function setRegularClosingHour($regularClosingHour = null)
    {
        $this->regularClosingHour = $regularClosingHour;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return User[]|ArrayCollection
     */
    public function getReferents()
    {
        return $this->referents;
    }

    /**
     * @param User[]|ArrayCollection $referents
     */
    public function setReferents($referents)
    {
        $this->referents = $referents;
    }

    /**
     * @param User $user
     */
    public function addReferent(User $user)
    {
        if (!$this->referents->contains($user)) {
            $this->referents->add($user);
        }
    }

    /**
     * @param User $user
     */
    public function removeReferent(User $user)
    {
        $this->referents->removeElement($user);
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
