<?php

namespace AppBundle\Form\Handler\Subscription;

use AppBundle\Entity\Procuration;
use AppBundle\Entity\User;
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
        EntityManager $entityManager
    ) {
        parent::__construct($formFactory, $formClassName);

        $this->userManager = $userManager;
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
        $user = $this->userManager->createUser();
        $user->setVotingOffice($this->entityManager->merge($data['office']['office']));
        $user->setCivility($data['contact']['civility']);
        $user->setFirstName($data['contact']['firstName']);
        $user->setLastName($data['contact']['lastName']);
        $user->setBirthDate($data['contact']['birthDate']);
        $user->setPhoneNumber($data['contact']['phoneNumber']);
        $user->setUsername($data['contact']['email']);
        $user->setAddress($data['address']);
        $user->setPlainPassword(mt_rand(PHP_INT_MIN, (int) (PHP_INT_MIN - mt_rand(10000, 12000))).time());
        $this->userManager->updateUser($user);

        $reason = $data['reason']['reason'];

        /** @var \AppBundle\Entity\Election $election */
        foreach ($data['election_rounds'] as $election) {
            $procuration = new Procuration();
            $procuration->setElectionRound($this->entityManager->merge($election));
            $procuration->setRequester($user);
            $procuration->setReason($reason);

            $this->entityManager->persist($procuration);
        }

        $this->entityManager->flush();

        $request->getSession()->getFlashBag()->add('firstName', $user->getFirstName());
        $this->setStoredData($request, []);

        return true;
    }
}
