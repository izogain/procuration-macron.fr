<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryBuilderAllByName()
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :role_admin') // Really yeah ...
            ->orWhere('u.roles LIKE :role_super_admin')
            ->orderBy('u.lastName', 'ASC')
            ->addOrderBy('u.firstName', 'ASC')
            ->setParameters([
                'role_admin' => '%ROLE_ADMIN%',
                'role_super_admin' => '%ROLE_SUPER_ADMIN%',
            ]);
    }
}
