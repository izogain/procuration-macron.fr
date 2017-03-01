<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Election;
use Doctrine\ORM\EntityRepository;

class ElectionRepository extends EntityRepository
{
    /**
     * @return Election[]
     */
    public function findAllByDateDesc()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.performanceDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryAllEnabledByDateAsc()
    {
        return $this->createQueryBuilder('e')
            ->where('e.active = :active')
            ->orderBy('e.performanceDate', 'ASC')
            ->setParameter('active', true);
    }
}
