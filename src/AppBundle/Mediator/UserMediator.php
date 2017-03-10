<?php

namespace AppBundle\Mediator;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;

class UserMediator
{
    const CIVILITY_MADAM = 0;
    const CIVILITY_MISTER = 1;

    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return array
     */
    public static function getCivilities()
    {
        return [
            static::CIVILITY_MADAM => 'Mme',
            static::CIVILITY_MISTER => 'M',
        ];
    }

    /**
     * @param int $value
     *
     * @return string
     */
    public function getCivility($value)
    {
        $civilities = static::getCivilities();

        return $civilities[$value] ?? '';
    }

    /**
     * @param User $user
     *
     * @return User[]|ArrayCollection
     */
    public function getAllWithCredentials(User $user)
    {
        if ($user->isSuperAdmin()) {
            return $this->repository->findAllWithRelationshipsByName();
        }

        return $this->repository->findAllForReferent($user);
    }
}
