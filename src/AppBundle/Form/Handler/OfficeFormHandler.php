<?php

namespace AppBundle\Form\Handler;

use AppBundle\Entity\Office;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class OfficeFormHandler
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
     * @param Office|null $office
     *
     * @return FormInterface
     */
    public function createForm(Office $office = null)
    {
        return $this->formFactory->create($this->formClassName, $office);
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

        return true;
    }
}
