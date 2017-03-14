<?php

namespace EnMarche\Bundle\CoreBundle\Mediator;

class AddressMediator
{
    const STREET_TYPE_STREET = 0;
    const STREET_TYPE_AVENUE = 1;
    const STREET_TYPE_PATH = 2;
    const STREET_TYPE_IMPASSE = 3;
    const STREET_TYPE_ALLEY = 4;
    const STREET_TYPE_VOICE = 5; // Yeah... sorry for this ...
    const STREET_TYPE_ROAD = 6;
    const STREET_TYPE_DOCK = 7;
    const STREET_TYPE_PLACE = 8;
    const STREET_TYPE_BOULEVARD = 9;
    const STREET_TYPE_COURSE = 10; // Yeah... sorry for this ...

    const STREET_REPEATER_BIS = 0;
    const STREET_REPEATER_TER = 1;
    const STREET_REPEATER_QUARTER = 2;
    const STREET_REPEATER_QUINQUIES = 3;

    /**
     * Return the full list of possible "street" types.
     *
     * @return array
     */
    public static function getStreetTypes()
    {
        return [
            static::STREET_TYPE_STREET => 'Rue',
            static::STREET_TYPE_AVENUE => 'Avenue',
            static::STREET_TYPE_BOULEVARD => 'Boulevard',
            static::STREET_TYPE_ALLEY => 'Allée',
            static::STREET_TYPE_PATH => 'Chemin',
            static::STREET_TYPE_IMPASSE => 'Impasse',
            static::STREET_TYPE_VOICE => 'Voie',
            static::STREET_TYPE_ROAD => 'Route',
            static::STREET_TYPE_DOCK => 'Quais',
            static::STREET_TYPE_PLACE => 'Place',
            static::STREET_TYPE_COURSE => 'Cours',
        ];
    }

    /**
     * Return the full list of possible street repeaters. Original list comes from impots.gouv.fr.
     *
     * @return array
     */
    public static function getStreetRepeaters()
    {
        $output = [
            static::STREET_REPEATER_BIS => 'bis',
            static::STREET_REPEATER_TER => 'ter',
            static::STREET_REPEATER_QUARTER => 'quarter',
            static::STREET_REPEATER_QUINQUIES => 'quinquies',
        ];

        $startIdx = 65; // See ASCII table http://www.asciitable.com/
        $optionMinValue = count($output);

        for ($i = 0; $i < 26; ++$i) {
            $output[$optionMinValue++] = chr($startIdx + $i);
        }

        return $output;
    }

    /**
     * @param int $streetRepeater
     *
     * @return string
     */
    public static function getStreetRepeaterLabel($streetRepeater)
    {
        return static::getStreetRepeaters()[$streetRepeater] ?? '';
    }

    /**
     * @param int $streetType
     *
     * @return string
     */
    public static function getStreetTypeLabel($streetType)
    {
        return static::getStreetTypes()[$streetType] ?? '';
    }
}
