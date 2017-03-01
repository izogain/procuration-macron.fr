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
            ->select('v', 'u', 'o', 'e', 'p')
            ->innerJoin('v.voter', 'u')
                ->innerJoin('u.votingOffice', 'o')
            ->innerJoin('v.election', 'e')
            ->leftJoin('v.procuration', 'p')
            ->where('u.enabled = :enabled')
            ->andWhere('e.active = :active_election')
            ->setParameters([
                'enabled' => true,
                'active_election' => true,
            ])
            ->orderBy('e.performanceDate', 'ASC')
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
            ->select('v', 'u', 'o', 'ref', 'e', 'p')
            ->innerJoin('v.voter', 'u')
                ->innerJoin('u.votingOffice', 'o')
                    ->innerJoin('o.referents', 'ref')
            ->innerJoin('v.election', 'e')
            ->leftJoin('v.procuration', 'p')
            ->where('ref.id = :user_id')
            ->andWhere('u.enabled = :enabled')
            ->andWhere('e.active = :active_election')
            ->setParameters([
                'user_id' => $userId,
                'enabled' => true,
                'active_election' => true,
            ])
            ->orderBy('e.performanceDate', 'ASC')
            ->addOrderBy('o.name', 'ASC')
            ->addOrderBy('u.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int      $electionId
     * @param string   $countryCode
     * @param string   $cityPostalCode
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderForAvailableForElectionInArea($electionId, $countryCode, $cityPostalCode)
    {
        $queryBuilder = $this->createQueryBuilder('v')
            ->select('v', 'voter', 'vo')
            ->innerJoin('v.voter', 'voter')
                ->innerJoin('voter.votingOffice', 'vo')
            ->innerJoin('v.election', 'e')
            ->leftJoin('v.procuration', 'p')
            ->where('p.id IS NULL')
            ->andWhere('e.id = :election_id')
            ->setParameter('election_id', $electionId);

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
