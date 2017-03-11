<?php

namespace AppBundle\Twig;

use AppBundle\Form\Handler\ProcurationAssignationFormHandler;

class ProcurationExtension extends \Twig_Extension
{
    /**
     * @var ProcurationAssignationFormHandler
     */
    protected $formAssignationFormHandler;

    /**
     * @param ProcurationAssignationFormHandler $procurationAssignationFormHandler
     */
    public function __construct(ProcurationAssignationFormHandler $procurationAssignationFormHandler)
    {
        $this->formAssignationFormHandler = $procurationAssignationFormHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('procuration_assignation_form', [$this->formAssignationFormHandler, 'createFormView']),
        ];
    }
}
