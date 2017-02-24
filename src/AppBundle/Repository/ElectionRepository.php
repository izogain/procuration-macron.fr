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
}
