<?php

namespace AppBundle\Form\Handler;

use AppBundle\Entity\Procuration;
use AppBundle\FPDI\FPDIWriter;
use AppBundle\Message\ProcurationAssignationMessage;
use Doctrine\ORM\EntityManager;
use EnMarche\Bundle\MailjetBundle\Client\MailjetClient;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ProcurationAssignationFormHandler
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
    protected $doctrinEntityManager;

    /**
     * @var FPDIWriter
     */
    protected $fpdiWriter;

    /**
     * @var MailjetClient
     */
    protected $mailjetClient;

    /**
     * @param FormFactoryInterface        $formFactory
     * @param string                      $formClassName
     * @param EntityManager               $doctrinEntityManager
     * @param FPDIWriter                  $fpdiWriter
     * @param MailjetClient               $mailjetClient
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        $formClassName,
        EntityManager $doctrinEntityManager,
        FPDIWriter $fpdiWriter,
        MailjetClient $mailjetClient
    ) {
        $this->formFactory = $formFactory;
        $this->formClassName = $formClassName;
        $this->doctrinEntityManager = $doctrinEntityManager;
        $this->fpdiWriter = $fpdiWriter;
        $this->mailjetClient = $mailjetClient;
    }

    /**
     * @param Procuration $procuration
     *
     * @return FormInterface
     */
    public function createForm(Procuration $procuration)
    {
        return $this->formFactory->create($this->formClassName, $procuration);
    }

    /**
     * @param Procuration $procuration
     *
     * @return \Symfony\Component\Form\FormView
     */
    public function createFormView(Procuration $procuration)
    {
        return $this->createForm($procuration)
            ->createView();
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

        $procuration = $form->getData();

        $this->doctrinEntityManager->persist($procuration);
        $this->doctrinEntityManager->flush();
        $this->fpdiWriter->generateForProcuration($procuration);

        $this->mailjetClient->sendMessage(ProcurationAssignationMessage::createFromModel($procuration));

        return true;
    }
}
