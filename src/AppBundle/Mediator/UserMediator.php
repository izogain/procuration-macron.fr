<?php

namespace AppBundle\Mediator;

class UserMediator
{
    const CIVILITY_MADAM = 0;
    const CIVILITY_MISTER = 1;

    /**
     * @return array
     */
    public static function getCivilities()
    {
        return [
            static::CIVILITY_MADAM => 'Mme',
            static::CIVILITY_MISTER => 'M',
        ];
    }

    /**
     * @param int $value
     *
     * @return string
     */
    public function getCivility($value)
    {
        $civilities = static::getCivilities();

        return $civilities[$value] ?? '';
    }
}
