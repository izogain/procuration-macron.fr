<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Office;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class OfficeRepository extends EntityRepository
{
    /**
     * @param User $user
     *
     * @return Office[]|ArrayCollection
     */
    public function findAllForReferent(User $user)
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.referents', 'r')
            ->where('r = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
