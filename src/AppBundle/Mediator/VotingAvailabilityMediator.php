<?php

namespace AppBundle\Mediator;

use AppBundle\Entity\User;
use AppBundle\Entity\VotingAvailability;
use AppBundle\Repository\VotingAvailabilityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

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
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @var string
     */
    protected $paginationPageParameterName;

    /**
     * @var int
     */
    protected $paginationSize;

    /**
     * @param VotingAvailabilityRepository $votingAvailabilityRepository
     * @param EntityManager                $entityManager
     * @param PaginatorInterface           $paginator
     * @param string                       $paginationPageParameterName
     * @param int                          $paginationSize
     */
    public function __construct(
        VotingAvailabilityRepository $votingAvailabilityRepository,
        EntityManager $entityManager,
        PaginatorInterface $paginator,
        $paginationPageParameterName,
        $paginationSize
    ) {
        $this->votingAvailabilityRepository = $votingAvailabilityRepository;
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
        $this->paginationPageParameterName = $paginationPageParameterName;
        $this->paginationSize = $paginationSize;
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return \Knp\Component\Pager\Pagination\AbstractPagination
     */
    public function getPaginatedActiveWithCredentials(Request $request, User $user)
    {
        if ($user->isSuperAdmin()) {
            $query = $this->votingAvailabilityRepository->findAllWithRelationships();
        } else {
            $query = $this->votingAvailabilityRepository->findByUserArea($user->getId());
        }

        return $this->paginator->paginate(
            $query,
            $request->query->getInt($this->paginationPageParameterName, 1),
            $this->paginationSize
        );
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
