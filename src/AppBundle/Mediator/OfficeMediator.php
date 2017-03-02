<?php

namespace AppBundle\Mediator;

use AppBundle\Entity\Office;
use AppBundle\Entity\User;
use AppBundle\Repository\OfficeRepository;

class OfficeMediator
{
    protected $officeRepository;

    /**
     * @param OfficeRepository $officeRepository
     */
    public function __construct(OfficeRepository $officeRepository)
    {
        $this->officeRepository = $officeRepository;
    }

    /**
     * @param User $user
     *
     * @return Office[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getAllWithCredentials(User $user)
    {
        if ($user->isSuperAdmin()) {
            return $this->officeRepository->findAll();
        }

        return $this->officeRepository->findAllForReferent($user);
    }
}
