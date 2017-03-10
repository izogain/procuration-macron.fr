<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Office;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class OfficeRepository extends EntityRepository
{
    /**
     * @param int $id
     *
     * @return Office|null
     */
    public function findWithReferents($id)
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.referents', 'r')
            ->where('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    /**
     * @param User $user
     *
     * @return Office[]|ArrayCollection
     */
    public function findAllForReferent(User $user)
    {
        return $this->getQueryBuilderAllForReferent($user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $user
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderAllForReferent(User $user)
    {
        return $this->createQueryBuilder('o')
             ->innerJoin('o.referents', 'r')
             ->where('r = :user')
             ->setParameter('user', $user);
    }
}
