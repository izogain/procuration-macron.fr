<?php

namespace AppBundle\Repository;

use AppBundle\Entity\VoterInvitation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class VoterInvitationRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryBuilderAllByName()
    {
        return $this->createQueryBuilder('v')
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
            ->innerJoin('v.sender', 's')
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
