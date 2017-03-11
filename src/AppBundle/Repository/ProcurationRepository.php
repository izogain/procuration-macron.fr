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
            ->select('p', 'er', 'e', 'r', 'o', 'v', 'u')
            ->innerJoin('p.electionRound', 'er')
                ->innerJoin('er.election', 'e')
            ->innerJoin('p.requester', 'r')
                ->innerJoin('r.votingOffice', 'o')
            ->leftJoin('p.votingAvailability', 'v')
                ->leftJoin('v.voter', 'u')
            ->where('er.active = 1')
            ->orderBy('p.createdAt', 'DESC')
            ->addOrderBy('o.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $userId
     *
     * @return Procuration[]|ArrayCollection
     */
    public function findByUserArea($userId)
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'er', 'e', 'r', 'o', 'v', 'ref')
            ->innerJoin('p.electionRound', 'er')
                ->innerJoin('er.election', 'e')
            ->innerJoin('p.requester', 'r')
                ->innerJoin('r.votingOffice', 'o')
                    ->innerJoin('o.referents', 'ref')
            ->leftJoin('p.votingAvailability', 'v')
                ->leftJoin('v.voter', 'u')
            ->where('er.active = :active_round')
            ->andWhere('ref.id = :user_id')
            ->orderBy('p.createdAt', 'DESC')
            ->addOrderBy('o.name', 'ASC')
            ->setParameters([
                'active_round' => true,
                'user_id' => $userId,
            ])
            ->getQuery()
            ->getResult();
    }
}
