<?php

namespace AppBundle\Entity;

class VotingAvailability
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var User
     */
    protected $voter;

    /**
     * @var Election
     */
    protected $election;

    /**
     * @var Procuration|null
     */
    protected $procuration;

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
    public function getVoter()
    {
        return $this->voter;
    }

    /**
     * @param User $voter
     */
    public function setVoter(User $voter)
    {
        $this->voter = $voter;
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
     * @return Procuration|null
     */
    public function getProcuration()
    {
        return $this->procuration;
    }

    /**
     * @param Procuration|null $procuration
     */
    public function setProcuration(Procuration $procuration = null)
    {
        $this->procuration = $procuration;
    }
}
