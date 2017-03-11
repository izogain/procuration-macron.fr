<?php

namespace AppBundle\Generator;

interface GeneratorInterface
{
    /**
     * Generate a random string.
     *
     * @return string
     */
    public function generate(): string;
}
