<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;

class User extends BaseUser
{
    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        parent::setUsername($username);

        return $this->setEmail($username);
    }
}
