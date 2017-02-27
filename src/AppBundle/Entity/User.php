<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;

class User extends BaseUser
{
    /**
     * @var int
     */
    protected $civility;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var \DateTime
     */
    protected $birthDate;

    /**
     * @var string
     */
    protected $phoneNumber;

    /**
     * @var Address
     */
    protected $address;

    /**
     * @var Office|null
     */
    protected $votingOffice;

    /**
     * @var VotingAvailability[]|ArrayCollection
     */
    protected $votingAvailabilities;

    /**
     * @var Procuration[]|ArrayCollection
     */
    protected $procurations;

    /**
     * For an admin profile, the user can handle numerous offices.
     *
     * @var Office[]|ArrayCollection
     */
    protected $officesInCharge;

    public function __construct()
    {
        parent::__construct();

        $this->address = new Address();
        $this->votingAvailabilities = new ArrayCollection();
        $this->procurations = new ArrayCollection();
        $this->officesInCharge = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s %s', ucwords($this->getFirstName()), mb_strtoupper($this->getLastName()));
    }

    /**
     * @return int
     */
    public function getCivility()
    {
        return $this->civility;
    }

    /**
     * @param int $civility
     */
    public function setCivility(int $civility)
    {
        $this->civility = $civility;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @param \DateTime $birthDate
     */
    public function setBirthDate(\DateTime $birthDate)
    {
        $this->birthDate = $birthDate;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber(string $phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
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
     * @return Office|null
     */
    public function getVotingOffice()
    {
        return $this->votingOffice;
    }

    /**
     * @param Office|null $votingOffice
     */
    public function setVotingOffice(Office $votingOffice = null)
    {
        $this->votingOffice = $votingOffice;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        parent::setUsername($username);

        return $this->setEmail($username);
    }

    /**
     * @return VotingAvailability[]|ArrayCollection
     */
    public function getVotingAvailabilities()
    {
        return $this->votingAvailabilities;
    }

    /**
     * @param VotingAvailability[]|ArrayCollection $votingAvailabilities
     */
    public function setVotingAvailabilities($votingAvailabilities)
    {
        $this->votingAvailabilities = $votingAvailabilities;
    }

    /**
     * @param VotingAvailability $votingAvailability
     */
    public function addVotingAvailability(VotingAvailability $votingAvailability)
    {
        if (!$this->votingAvailabilities->contains($votingAvailability)) {
            $this->votingAvailabilities->add($votingAvailability);
        }

        $votingAvailability->setVoter($this);
    }

    /**
     * @param VotingAvailability $votingAvailability
     */
    public function removeVotingAvailability(VotingAvailability $votingAvailability)
    {
        $this->votingAvailabilities->removeElement($votingAvailability);
    }

    /**
     * @return Procuration[]|ArrayCollection
     */
    public function getProcurations()
    {
        return $this->procurations;
    }

    /**
     * @param Procuration[]|ArrayCollection $procurations
     */
    public function setProcurations($procurations)
    {
        $this->procurations = $procurations;
    }

    /**
     * @param Procuration $procuration
     */
    public function addProcuration(Procuration $procuration)
    {
        if (!$this->procurations->contains($procuration)) {
            $this->procurations->add($procuration);
        }

        $procuration->setRequester($this);
    }

    /**
     * @param Procuration $procuration
     */
    public function removeProcuration(Procuration $procuration)
    {
        $this->procurations->removeElement($procuration);
    }

    /**
     * @return Office[]|ArrayCollection
     */
    public function getOfficesInCharge()
    {
        return $this->officesInCharge;
    }

    /**
     * @param Office[]|ArrayCollection $officesInCharge
     */
    public function setOfficesInCharge($officesInCharge)
    {
        $this->officesInCharge = $officesInCharge;
    }

    /**
     * @param Office $office
     */
    public function addOfficeInCharge(Office $office)
    {
        if (!$this->officesInCharge->contains($office)) {
            $this->officesInCharge->add($office);
        }
    }

    /**
     * @param Office $office
     */
    public function removeOfficeInCharge(Office $office)
    {
        $this->officesInCharge->removeElement($office);
    }
}
