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
     * @var ElectionRound
     */
    protected $electionRound;

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
     * @return ElectionRound
     */
    public function getElectionRound()
    {
        return $this->electionRound;
    }

    /**
     * @param ElectionRound $electionRound
     */
    public function setElectionRound(ElectionRound $electionRound)
    {
        $this->electionRound = $electionRound;
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
