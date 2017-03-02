<?php

namespace AppBundle\Entity;

class Address
{
    /**
     * @var int|null
     */
    protected $streetNumber;

    /**
     * @var int|null
     */
    protected $streetRepeater;

    /**
     * @var int|null
     */
    protected $streetType;

    /**
     * @var string
     */
    protected $streetName;

    /**
     * @var string
     */
    protected $postalCode;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $countryCode = 'FR';

    /**
     * @return int|null
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * @param int|null $streetNumber
     */
    public function setStreetNumber($streetNumber = null)
    {
        $this->streetNumber = $streetNumber;
    }

    /**
     * @return int|null
     */
    public function getStreetRepeater()
    {
        return $this->streetRepeater;
    }

    /**
     * @param int|null $streetRepeater
     */
    public function setStreetRepeater($streetRepeater = null)
    {
        $this->streetRepeater = $streetRepeater;
    }

    /**
     * @return int|null
     */
    public function getStreetType()
    {
        return $this->streetType;
    }

    /**
     * @param int|null $streetType
     */
    public function setStreetType($streetType = null)
    {
        $this->streetType = $streetType;
    }

    /**
     * @return string
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * @param string $streetName
     */
    public function setStreetName(string $streetName)
    {
        $this->streetName = $streetName;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode(string $postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode(string $countryCode)
    {
        $this->countryCode = $countryCode;
    }
}
