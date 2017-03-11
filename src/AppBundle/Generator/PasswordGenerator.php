<?php

namespace AppBundle\Generator;

class PasswordGenerator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(): string
    {
        return sha1(mt_rand(10000, 498954385).time());
    }
}
