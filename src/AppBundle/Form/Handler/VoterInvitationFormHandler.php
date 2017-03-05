<?php

namespace AppBundle\Form\Handler;

use AppBundle\Entity\User;
use AppBundle\Entity\VoterInvitation;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class VoterInvitationFormHandler
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
     * @param FormFactoryInterface $formFactory
     * @param string               $formClassName
     * @param EntityManager        $entityManager
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        $formClassName,
        EntityManager $entityManager
    ) {
        $this->formFactory = $formFactory;
        $this->formClassName = $formClassName;
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $sender
     *
     * @return FormInterface
     */
    public function createForm(User $sender)
    {
        $voterInvitation = new VoterInvitation();
        $voterInvitation->setSender($sender);

        return $this->formFactory->create($this->formClassName, $voterInvitation);
    }

    /**
     * @param FormInterface $form
     * @param Request       $request
     *
     * @return bool
     */
    public function process(FormInterface $form, Request $request)
    {
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return false;
        }

        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();

        // TODO email sending

        return true;
    }
}
