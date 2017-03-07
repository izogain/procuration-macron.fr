<?php

namespace AppBundle\Form\Handler\Subscription;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class SubscriptionElectionRoundFormHandler extends AbstractFormHandler
{
    const STEP_KEY_NAME = 'elections';

    /**
     * @inheritdoc
     */
    protected static function getStepKeyName()
    {
        return static::STEP_KEY_NAME;
    }

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @inheritDoc
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        $formClassName,
        EntityManager $entityManager
    ) {
        parent::__construct($formFactory, $formClassName);

        $this->entityManager = $entityManager;
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

        $data = [];

        foreach ($form->getData()['elections_round'] as $electionRound) {
            $this->entityManager->detach($electionRound);

            $data[] = $electionRound;
        }

        $this->appendToSession($request, static::getStepKeyName(), $data);

        return true;
    }
}
