<?php

namespace AppBundle\Entity;

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
     * @var \DateTime
     */
    protected $performanceDate;

    /**
     * @var bool
     */
    protected $active = true;

    public function __toString()
    {
        return (string) $this->getLabel();
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
