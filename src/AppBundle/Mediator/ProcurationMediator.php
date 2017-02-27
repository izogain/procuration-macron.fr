<?php

namespace AppBundle\Mediator;

use AppBundle\Entity\Procuration;
use AppBundle\Entity\User;
use AppBundle\Repository\ProcurationRepository;
use Doctrine\ORM\EntityManager;

class ProcurationMediator
{
    /**
     * @var ProcurationRepository
     */
    protected $procurationRepository;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param ProcurationRepository $procurationRepository
     * @param EntityManager         $entityManager
     */
    public function __construct(
        ProcurationRepository $procurationRepository,
        EntityManager $entityManager
    ) {
        $this->procurationRepository = $procurationRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     *
     * @return Procuration[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getAllWithCredentials(User $user)
    {
        if ($user->isSuperAdmin()) {
            return $this->procurationRepository->findAllWithRelationships();
        }

        return $this->procurationRepository->findByUserArea($user->getId());
    }

    /**
     * @param Procuration $procuration
     *
     * @return bool
     */
    public function isDeletable(Procuration $procuration)
    {
        return null === $procuration->getVotingAvailability();
    }

    /**
     * @param Procuration $procuration
     * @param bool        $withFlush
     */
    public function delete(Procuration $procuration, $withFlush = true)
    {
        $this->entityManager->remove($procuration);

        if ($withFlush) {
            $this->entityManager->flush();
        }

        // TODO send email to $procuration->getRequester()
    }

    /**
     * @param Procuration $procuration
     * @param bool        $withFlush
     */
    public function unbind(Procuration $procuration, $withFlush = true)
    {
        $procuration->setVotingAvailability(null);

        $this->entityManager->persist($procuration);

        if ($withFlush) {
            $this->entityManager->flush();
        }
    }
}
