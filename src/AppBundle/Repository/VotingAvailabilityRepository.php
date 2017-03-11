<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Election;
use AppBundle\Entity\VotingAvailability;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class VotingAvailabilityRepository extends EntityRepository
{
    /**
     * @return VotingAvailability[]|ArrayCollection
     */
    public function findAllWithRelationships()
    {
        return $this->createQueryBuilder('v')
            ->select('v', 'u', 'o', 'r', 'e', 'p')
            ->innerJoin('v.voter', 'u')
                ->innerJoin('u.votingOffice', 'o')
            ->innerJoin('v.electionRound', 'r')
                ->innerJoin('r.election', 'e')
            ->leftJoin('v.procuration', 'p')
            ->where('r.active = :active_round')
            ->setParameters([
                'active_round' => true,
            ])
            ->orderBy('r.performanceDate', 'ASC')
            ->addOrderBy('o.name', 'ASC')
            ->addOrderBy('u.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $userId
     *
     * @return VotingAvailability[]|ArrayCollection
     */
    public function findByUserArea($userId)
    {
        return $this->createQueryBuilder('v')
            ->select('v', 'u', 'o', 'ref', 'r', 'e', 'p')
            ->innerJoin('v.voter', 'u')
                ->innerJoin('u.votingOffice', 'o')
                    ->innerJoin('o.referents', 'ref')
            ->innerJoin('v.electionRound', 'r')
                ->innerJoin('r.election', 'e')
            ->leftJoin('v.procuration', 'p')
            ->where('ref.id = :user_id')
            ->andWhere('r.active = :active_round')
            ->setParameters([
                'user_id' => $userId,
                'active_round' => true,
            ])
            ->orderBy('r.performanceDate', 'ASC')
            ->addOrderBy('o.name', 'ASC')
            ->addOrderBy('u.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int    $electionId
     * @param string $countryCode
     * @param string $cityPostalCode
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderForAvailableForElectionInArea($electionId, $countryCode, $cityPostalCode)
    {
        $queryBuilder = $this->createQueryBuilder('v')
            ->select('v', 'voter', 'vo')
            ->innerJoin('v.voter', 'voter')
                ->innerJoin('voter.votingOffice', 'vo')
            ->innerJoin('v.electionRound', 'r')
                ->innerJoin('r.election', 'e')
            ->leftJoin('v.procuration', 'p')
            ->where('p.id IS NULL')
            ->andWhere('r.id = :round_id')
            ->setParameter('round_id', $electionId);

        if ($countryCode != 'FR') {
            $queryBuilder->andWhere('vo.address.countryCode = :country_code')
                ->setParameter('country_code', $countryCode);
        } else {
            $queryBuilder->andWhere('vo.address.postalCode = :postal_code')
                ->setParameter('postal_code', $cityPostalCode);
        }

        return $queryBuilder;
    }
}
