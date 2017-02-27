<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Procuration;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class ProcurationRepository extends EntityRepository
{
    /**
     * @return Procuration[]|ArrayCollection
     */
    public function findAllWithRelationships()
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'e', 'r', 'o', 'v', 'u')
            ->innerJoin('p.election', 'e')
            ->innerJoin('p.requester', 'r')
                ->innerJoin('r.votingOffice','o')
            ->leftJoin('p.votingAvailability', 'v')
                ->leftJoin('v.voter', 'u')
            ->orderBy('p.createdAt', 'DESC')
            ->addOrderBy('o.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByUserArea($userId)
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'e', 'r', 'o', 'v', 'ref')
            ->innerJoin('p.election', 'e')
            ->innerJoin('p.requester', 'r')
                ->innerJoin('r.votingOffice','o')
                    ->innerJoin('o.referents', 'ref')
            ->leftJoin('p.votingAvailability', 'v')
                ->leftJoin('v.voter', 'u')
            ->where('ref.id = :user_id')
            ->orderBy('p.createdAt', 'DESC')
            ->addOrderBy('o.name', 'ASC')
            ->setParameter('user_id', $userId)
            ->getQuery()
            ->getResult();
    }
}