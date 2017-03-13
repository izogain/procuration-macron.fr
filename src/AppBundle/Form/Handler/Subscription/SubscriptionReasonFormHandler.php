<?php

namespace AppBundle\Form\Handler\Subscription;

use AppBundle\Entity\Procuration;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class SubscriptionReasonFormHandler extends AbstractFormHandler
{
    const STEP_KEY_NAME = 'reason';

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        $formClassName,
        UserManager $userManager,
        UserRepository $userRepository,
        EntityManager $entityManager
    ) {
        parent::__construct($formFactory, $formClassName);

        $this->userManager = $userManager;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    protected static function getStepKeyName()
    {
        return static::STEP_KEY_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function process(FormInterface $form, Request $request)
    {
        if (!parent::process($form, $request)) {
            return false;
        }

        $data = $this->getStoredData($request);

        /** @var User $user */
        if (!$user = $this->userRepository->findOneByEmailWithProcurations($data['contact']['email'])) {
            $user = $this->userManager->createUser();
            $user->setVotingOffice($this->entityManager->merge($data['office']['office']));
            $user->setCivility($data['contact']['civility']);
            $user->setFirstName($data['contact']['firstName']);
            $user->setLastName($data['contact']['lastName']);
            $user->setBirthDate($data['contact']['birthDate']);
            $user->setPhoneNumber($data['contact']['phoneNumber']);
            $user->setEmail($data['contact']['email']);
            $user->setAddress($data['address']);
            $user->setPlainPassword(mt_rand(PHP_INT_MIN, (int) (PHP_INT_MIN - mt_rand(10000, 12000))).time());

            $this->userManager->updateUser($user);
        }

        $electionRounds = $this->getFilteredElectionRounds($user, $data['election_rounds']);
        $reason = $data['reason']['reason'];

        /* @var \AppBundle\Entity\Election $election */
        foreach ($electionRounds as $electionRound) {
            $procuration = new Procuration();
            $procuration->setElectionRound($electionRound);
            $procuration->setRequester($user);
            $procuration->setReason($reason);

            $this->entityManager->persist($procuration);
        }

        $this->entityManager->flush();

        $request->getSession()->getFlashBag()->add('firstName', $user->getFirstName());
        $this->setStoredData($request, []);

        return true;
    }

    /**
     * @param User  $user
     * @param array $submittedElectionRounds
     *
     * @return ArrayCollection
     */
    private function getFilteredElectionRounds(User $user, array $submittedElectionRounds)
    {
        $electionRoundsToAdd = new ArrayCollection();

        foreach ($submittedElectionRounds as $electionRound) {
            $electionRoundsToAdd->add($this->entityManager->merge($electionRound));
        }

        foreach ($user->getProcurations() as $procuration) {
            $electionRound = $procuration->getElectionRound();

            if ($electionRoundsToAdd->contains($electionRound)) {
                $electionRoundsToAdd->removeElement($electionRound);
            }
        }

        return $electionRoundsToAdd;
    }
}
