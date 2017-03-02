<?php

namespace AppBundle\Repository;

use AppBundle\Entity\ElectionRound;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class ElectionRoundRepository extends EntityRepository
{
    /**
     * @return ElectionRound[]|ArrayCollection
     */
    public function findAllByDateDesc()
    {
        return $this->createQueryBuilder('r')
            ->select('r', 'e')
            ->innerJoin('r.election', 'e')
            ->orderBy('r.performanceDate', 'ASC')
            ->getQuery()
            ->getResult();
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
}
