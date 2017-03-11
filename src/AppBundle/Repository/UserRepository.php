<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryBuilderAllByName()
    {
        return $this->createQueryBuilder('u')
            ->where('COUNT(u.officesInCharge) > 0')
            ->orWhere('u.sup')
            ->orderBy('u.lastName', 'ASC')
            ->addOrderBy('u.firstName', 'ASC')
            ->setParameters([
                'role_admin' => '%ROLE_ADMIN%',
                'role_super_admin' => '%ROLE_SUPER_ADMIN%',
            ]);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderAllWithRelationshipsByName()
    {
        return $this->createQueryBuilder('u')
            ->select('u', 'o')
            ->leftJoin('u.officesInCharge', 'o')
            ->orderBy('u.lastName', 'ASC')
            ->addOrderBy('u.firstName', 'ASC');
    }

    /**
     * @param User $user
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderAllForReferent(User $user)
    {
        return $this->createQueryBuilder('u')
            ->select('u', 'o')
            ->innerJoin('u.officesInCharge', 'o')
                ->innerJoin('o.referents', 'r')
            ->where('r.id = :user')
            ->setParameter('user', $user)
            ->orderBy('u.lastName', 'ASC')
            ->addOrderBy('u.firstName', 'ASC');
    }

    /**
     * @param int $id
     *
     * @return User|null
     */
    public function findOneWithRelations($id): ?User
    {
        return $this->createQueryBuilder('u')
            ->select('u', 'o')
            ->leftJoin('u.officesInCharge', 'o')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function findOneByEmail($email)
    {
        return $this->findOneBy(['email' => $email]);
    }
}
