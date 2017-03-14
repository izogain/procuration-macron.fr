<?php

namespace EnMarche\Bundle\CoreBundle\Model;

interface AddressInterface
{
    /**
     * @return int|null
     */
    public function getStreetNumber();

    /**
     * @param int|null $streetNumber
     */
    public function setStreetNumber($streetNumber = null);

    /**
     * @return int|null
     */
    public function getStreetRepeater();

    /**
     * @param int|null $streetRepeater
     */
    public function setStreetRepeater($streetRepeater = null);

    /**
     * @return int|null
     */
    public function getStreetType();

    /**
     * @param int|null $streetType
     */
    public function setStreetType($streetType = null);

    /**
     * @return string|null
     */
    public function getStreetName();

    /**
     * @param string|null $streetName
     */
    public function setStreetName($streetName = null);

    /**
     * @return string
     */
    public function getPostalCode();

    /**
     * @param string $postalCode
     */
    public function setPostalCode(string $postalCode);

    /**
     * @return string
     */
    public function getInseeCityCode();

    /**
     * @param string $city
     */
    public function setInseeCityCode(string $city);

    /**
     * @return string
     */
    public function getCityName();

    /**
     * @param string $cityName
     */
    public function setCityName($cityName);

    /**
     * @return string
     */
    public function getCountryCode();

    /**
     * @param string $countryCode
     */
    public function setCountryCode(string $countryCode);
}
