<?php

namespace EnMarche\Bundle\CoreBundle\Entity;

use EnMarche\Bundle\CoreBundle\Intl\FranceCitiesBundle;
use EnMarche\Bundle\CoreBundle\Model\AddressInterface;

class Address implements AddressInterface
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
     * @var string|null
     */
    protected $streetName;

    /**
     * @var string
     */
    protected $postalCode;

    /**
     * @var string
     */
    protected $inseeCityCode;

    /**
     * @var string|null
     */
    protected $cityName;

    /**
     * @var string
     */
    protected $countryCode = 'FR';

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * @param string|null $streetName
     */
    public function setStreetName($streetName = null)
    {
        $this->streetName = $streetName;
    }

    /**
     * {@inheritdoc}
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setPostalCode(string $postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getInseeCityCode()
    {
        return $this->inseeCityCode;
    }

    /**
     * @param string $inseeCityCode
     */
    public function setInseeCityCode(string $inseeCityCode)
    {
        $this->inseeCityCode = $inseeCityCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getCityName()
    {
        if ($this->cityName) {
            return $this->cityName;
        }

        if ($this->postalCode && $this->inseeCityCode) {
            $this->cityName = FranceCitiesBundle::getCity($this->postalCode, static::getInseeCode($this->inseeCityCode));
        }

        return $this->cityName;
    }

    /**
     * {@inheritdoc}
     */
    public function setCityName($cityName)
    {
        $this->cityName = $cityName;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setCountryCode(string $countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * Returns the french national INSEE code from the city code.
     *
     * @param string $cityCode
     *
     * @return string
     */
    private static function getInseeCode(string $cityCode)
    {
        list(, $inseeCode) = explode('-', $cityCode);

        return $inseeCode;
    }
}
