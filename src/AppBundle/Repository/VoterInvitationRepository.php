<?php

namespace AppBundle\Repository;

use AppBundle\Entity\VoterInvitation;
use Doctrine\ORM\EntityRepository;

class VoterInvitationRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryBuilderAllByName()
    {
        return $this->createQueryBuilder('v')
            ->select('v', 's')
            ->innerJoin('v.sender', 's')
            ->orderBy('v.lastName', 'ASC')
            ->addOrderBy('v.firstName', 'ASC');
    }

    /**
     * @param string $userId
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryBuilderSentBy($userId)
    {
        return $this->queryBuilderAllByName()
            ->where('s.id = :user_id')
            ->setParameter('user_id', $userId);
    }

    /**
     * @param string $hash
     *
     * @return VoterInvitation|null
     */
    public function findOneActiveByHash($hash)
    {
        return $this->findOneBy(['hash' => $hash, 'active' => true]);
    }
}
