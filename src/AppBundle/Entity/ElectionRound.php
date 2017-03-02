<?php

namespace AppBundle\Entity;

class ElectionRound
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Election
     */
    protected $election;

    /**
     * @var \DateTime
     */
    protected $performanceDate;

    /**
     * @var bool
     */
    protected $active = false;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return \DateTime
     */
    public function getPerformanceDate()
    {
        return $this->performanceDate;
    }

    /**
     * @param \DateTime $performanceDate
     */
    public function setPerformanceDate(\DateTime $performanceDate)
    {
        $this->performanceDate = $performanceDate;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active)
    {
        $this->active = $active;
    }
}
