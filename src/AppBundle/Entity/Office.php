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
     * @var Address
     */
    protected $address;

    /**
     * @var User[]|ArrayCollection
     */
    protected $referents;

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
}
