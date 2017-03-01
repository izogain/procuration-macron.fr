<?php

declare(strict_types=1);

namespace AppBundle\Form\Handler;

use AppBundle\Entity\Election;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ElectionFormHandler
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
     * @param Election|null $data
     * @param array         $options
     *
     * @return FormInterface
     */
    public function createForm(Election $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create($this->formClassName, $data, $options);
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

        if (!$form->isValid()) {
            return false;
        }

        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();

        return true;
    }
}
