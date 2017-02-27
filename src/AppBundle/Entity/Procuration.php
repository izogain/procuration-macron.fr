<?php

namespace AppBundle\Entity;

class Procuration
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var User
     */
    protected $requester;

    /**
     * @var Election
     */
    protected $election;

    /**
     * @var VotingAvailability|null
     */
    protected $votingAvailability;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

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
     * @return User
     */
    public function getRequester()
    {
        return $this->requester;
    }

    /**
     * @param User $requester
     */
    public function setRequester(User $requester)
    {
        $this->requester = $requester;
    }

    /**
     * @return Election
     */
    public function getElection()
    {
        return $this->election;
    }

    /**
     * @param Election $election
     */
    public function setElection(Election $election)
    {
        $this->election = $election;
    }

    /**
     * @return VotingAvailability|null
     */
    public function getVotingAvailability()
    {
        return $this->votingAvailability;
    }

    /**
     * @param VotingAvailability|null $votingAvailability
     */
    public function setVotingAvailability(VotingAvailability $votingAvailability = null)
    {
        $this->votingAvailability = $votingAvailability;
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