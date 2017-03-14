<?php

declare(strict_types=1);

namespace AppBundle\Form\Handler;

use AppBundle\Entity\ElectionRound;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ElectionRoundFormHandler
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
    public function __construct(FormFactoryInterface $formFactory, $formClassName, EntityManager $entityManager)
    {
        $this->formFactory = $formFactory;
        $this->formClassName = $formClassName;
        $this->entityManager = $entityManager;
    }

    /**
     * @param ElectionRound|null $data
     *
     * @return FormInterface
     */
    public function createForm(ElectionRound $data = null): FormInterface
    {
        return $this->formFactory->create($this->formClassName, $data);
    }

    /**
     * @param FormInterface $form
     * @param Request       $request
     *
     * @return bool
     */
    public function process(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return false;
        }

        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();

        return true;
    }
}
