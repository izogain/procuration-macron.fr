<?php

namespace Tests\AppBundle;

use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createUserMock()
    {
        return $this->createMock(\AppBundle\Entity\User::class);
    }
}
