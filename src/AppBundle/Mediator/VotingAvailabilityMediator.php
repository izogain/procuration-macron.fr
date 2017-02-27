<?php

namespace AppBundle\Mediator;

use AppBundle\Entity\User;
use AppBundle\Entity\VotingAvailability;
use AppBundle\Repository\VotingAvailabilityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

class VotingAvailabilityMediator
{
    /**
     * @var VotingAvailabilityRepository
     */
    protected $votingAvailabilityRepository;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param VotingAvailabilityRepository $votingAvailabilityRepository
     * @param EntityManager                $entityManager
     */
    public function __construct(
        VotingAvailabilityRepository $votingAvailabilityRepository,
        EntityManager $entityManager
    ) {
        $this->votingAvailabilityRepository = $votingAvailabilityRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     *
     * @return VotingAvailability[]|ArrayCollection
     */
    public function getAllActiveWithCredentials(User $user)
    {
        if ($user->isSuperAdmin()) {
            return $this->votingAvailabilityRepository->findAllWithRelationships();
        }

        return $this->votingAvailabilityRepository->findByUserArea($user->getId());
    }

    /**
     * @param VotingAvailability $votingAvailability
     * @param bool               $withFlush
     */
    public function delete(VotingAvailability $votingAvailability, $withFlush = true)
    {
        $this->entityManager->remove($votingAvailability);

        if ($withFlush) {
            $this->entityManager->flush();
        }

        // TODO send email to $votingAvailability->getVoter()
    }
}