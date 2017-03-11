<?php

namespace AppBundle\Form\Handler;

use AppBundle\Entity\User;
use AppBundle\Generator\GeneratorInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class UserFormHandler
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
     * @var GeneratorInterface
     */
    protected $passwordGenerator;

    /**
     * @param FormFactoryInterface $formFactory
     * @param string               $formClassName
     * @param EntityManager        $entityManager
     * @param GeneratorInterface   $passwordGenerator
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        $formClassName,
        EntityManager $entityManager,
        GeneratorInterface $passwordGenerator
    ) {
        $this->formFactory = $formFactory;
        $this->formClassName = $formClassName;
        $this->entityManager = $entityManager;
        $this->passwordGenerator = $passwordGenerator;
    }

    /**
     * @param User      $editor
     * @param User|null $user
     *
     * @return FormInterface
     */
    public function createForm(User $editor, User $user = null)
    {
        if (!$user) {
            $user = new User();
            $user->setPlainPassword($this->passwordGenerator->generate());
        }

        return $this->formFactory->create($this->formClassName, $user, ['editor' => $editor]);
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

        $editor = $form->getConfig()->getOption('editor');
        $user = $form->getData();

        if ($editor->getId() == $user->getId()) {
            $form->addError(new FormError('Pour des raisons de sécurité, vous ne pouvez modifier votre propre compte.'));

            return false;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return true;
    }
}
