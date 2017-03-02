<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Election
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var ElectionRound[]|ArrayCollection
     */
    protected $rounds;

    public function __construct()
    {
        $this->rounds = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getLabel();
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
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
    }

    /**
     * @return ElectionRound[]|ArrayCollection
     */
    public function getRounds()
    {
        return $this->rounds;
    }

    /**
     * @param ElectionRound[]|ArrayCollection $rounds
     */
    public function setRounds($rounds)
    {
        $this->rounds = $rounds;
    }
}
