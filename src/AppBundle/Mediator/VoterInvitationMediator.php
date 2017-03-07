<?php

namespace AppBundle\Mediator;

use AppBundle\Entity\VoterInvitation;
use Doctrine\ORM\EntityManager;

class VoterInvitationMediator
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param VoterInvitation $voterInvitation
     * @param bool            $flush
     */
    public function consume(VoterInvitation $voterInvitation, $flush = true)
    {
        $voterInvitation->setActive(false);
        $this->entityManager->persist($voterInvitation);

        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
