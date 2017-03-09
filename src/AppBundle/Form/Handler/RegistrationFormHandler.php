<?php

namespace AppBundle\Form\Handler;

use AppBundle\Entity\User;
use AppBundle\Entity\VoterInvitation;
use AppBundle\Entity\VotingAvailability;
use AppBundle\Mediator\VoterInvitationMediator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class RegistrationFormHandler
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var string
     */
    protected $formClassName;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var VoterInvitationMediator
     */
    protected $voterInvitationMediator;

    /**
     * @param FormFactoryInterface    $formFactory
     * @param string                  $formClassName
     * @param EntityManager           $entityManager
     * @param VoterInvitationMediator $voterInvitationMediator
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        $formClassName,
        EntityManager $entityManager,
        VoterInvitationMediator $voterInvitationMediator
    ) {
        $this->formFactory = $formFactory;
        $this->formClassName = $formClassName;
        $this->entityManager = $entityManager;
        $this->voterInvitationMediator = $voterInvitationMediator;
    }

    /**
     * @param VoterInvitation $voterInvitation
     *
     * @return FormInterface
     */
    public function createFormFromVoterInvitation(VoterInvitation $voterInvitation)
    {
        $user = new User();
        $user->setCivility($voterInvitation->getCivility());
        $user->setUsername($voterInvitation->getEmail());
        $user->setFirstName($voterInvitation->getFirstName());
        $user->setLastName($voterInvitation->getLastName());
        $user->setPlainPassword(sha1(mt_rand(10000, 498954385).time()));
        $user->setEnabled(true);

        return $this->formFactory->create($this->formClassName, $user);
    }

    /**
     * @param FormInterface   $form
     * @param Request         $request
     * @param VoterInvitation $voterInvitation
     *
     * @return bool
     */
    public function process(FormInterface $form, Request $request, VoterInvitation $voterInvitation)
    {
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return false;
        }

        /** @var User $user */
        $user = $form->getData();

        foreach ($form->get('elections')->getData() as $electionRound) {
            $votingAvailability = new VotingAvailability();
            $votingAvailability->setElectionRound($electionRound);

            $user->addVotingAvailability($votingAvailability);
        }

        $voterInvitation->setActive(false);
        $this->entityManager->persist($voterInvitation);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return true;
    }
}
