<?php

namespace AppBundle\Repository;

use AppBundle\Entity\ElectionRound;
use Doctrine\ORM\EntityRepository;

class ElectionRoundRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderfindAllByDateDesc()
    {
        return $this->createQueryBuilder('r')
            ->select('r', 'e')
            ->innerJoin('r.election', 'e')
            ->orderBy('r.performanceDate', 'ASC');
    }

    /**
     * @param int $id
     *
     * @return ElectionRound|null
     */
    public function findOneWithRelations($id)
    {
        return $this->createQueryBuilder('r')
            ->select('r', 'e')
            ->innerJoin('r.election', 'e')
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryAllUpcomingEnabledByDateAsc()
    {
        return $this->createQueryBuilder('r')
            ->select('r', 'e')
            ->innerJoin('r.election', 'e')
            ->where('r.active = :active')
            ->andWhere('r.performanceDate > :today')
            ->orderBy('r.performanceDate', 'ASC')
            ->setParameter('active', true)
            ->setParameter('today', new \DateTime());
    }
}
