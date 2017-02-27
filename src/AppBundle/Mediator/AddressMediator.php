<?php

namespace AppBundle\Mediator;

class AddressMediator
{
    const STREET_TYPE_STREET = 0;
    const STREET_TYPE_AVENUE = 1;
    const STREET_TYPE_PATH = 2;
    const STREET_TYPE_IMPASSE = 3;

    const STREET_REPEATER_BIS = 0;
    const STREET_REPEATER_TER = 1;
    const STREET_REPEATER_QUARTER = 2;
    const STREET_REPEATER_QUINQUIES = 3;

    /**
     * Return the full list of possible "street" types
     *
     * @return array
     */
    public static function getStreetTypes()
    {
        return [
            static::STREET_TYPE_STREET => 'Rue',
            static::STREET_TYPE_AVENUE => 'Avenue',
            static::STREET_TYPE_PATH => 'Chemin',
            static::STREET_TYPE_IMPASSE => 'Impasse',
        ];
    }

    /**
     * Return the full list of possible street repeaters. Original list comes from impots.gouv.fr
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
}