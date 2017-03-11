<?php

namespace AppBundle\Form\Handler\Subscription;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractFormHandler
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
     * @return string
     */
    abstract protected static function getStepKeyName();

    /**
     * @param FormFactoryInterface $formFactory
     * @param string               $formClassName
     */
    public function __construct(FormFactoryInterface $formFactory, $formClassName)
    {
        $this->formFactory = $formFactory;
        $this->formClassName = $formClassName;
    }

    /**
     * @return FormInterface
     */
    public function createForm()
    {
        return $this->formFactory->create($this->formClassName);
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

        $this->appendToSession($request, static::getStepKeyName(), $form->getData());

        return true;
    }

    /**
     * @param Request $request
     * @param string  $key
     * @param mixed   $data
     */
    protected function appendToSession(Request $request, $key, $data)
    {
        $subscription = $this->getStoredData($request);
        $subscription[$key] = $data;

        $this->setStoredData($request, $subscription);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function getStoredData(Request $request)
    {
        return $request->getSession()->get(SubscriptionFormHandler::STORAGE_KEY_NAME, []);
    }

    /**
     * @param Request $request
     * @param array   $data
     */
    protected function setStoredData(Request $request, array $data = [])
    {
        return $request->getSession()->set(SubscriptionFormHandler::STORAGE_KEY_NAME, $data);
    }
}
