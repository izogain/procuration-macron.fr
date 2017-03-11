<?php

namespace AppBundle\Form\Handler;

use AppBundle\Entity\User;
use AppBundle\Entity\VoterInvitation;
use AppBundle\Entity\VotingAvailability;
use AppBundle\Generator\GeneratorInterface;
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
     * @var GeneratorInterface
     */
    protected $passwordGenerator;

    /**
     * @param FormFactoryInterface    $formFactory
     * @param string                  $formClassName
     * @param EntityManager           $entityManager
     * @param VoterInvitationMediator $voterInvitationMediator
     * @param GeneratorInterface      $passwordGenerator
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        $formClassName,
        EntityManager $entityManager,
        VoterInvitationMediator $voterInvitationMediator,
        GeneratorInterface $passwordGenerator
    ) {
        $this->formFactory = $formFactory;
        $this->formClassName = $formClassName;
        $this->entityManager = $entityManager;
        $this->voterInvitationMediator = $voterInvitationMediator;
        $this->passwordGenerator = $passwordGenerator;
    }

    /**
     * @param VoterInvitation $voterInvitation
     * @param User            $user
     *
     * @return FormInterface
     */
    public function createFormFromVoterInvitation(VoterInvitation $voterInvitation, User $user = null)
    {
        if (!$user) {
            $user = new User();
            $user->setCivility($voterInvitation->getCivility());
            $user->setUsername($voterInvitation->getEmail());
            $user->setFirstName($voterInvitation->getFirstName());
            $user->setLastName($voterInvitation->getLastName());
            $user->setPlainPassword($this->passwordGenerator->generate());
        }

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
