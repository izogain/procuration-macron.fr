<?php

namespace AppBundle\Form\Handler\Subscription;

use AppBundle\Entity\Address;
use Symfony\Component\Form\FormInterface;

class SubscriptionAddressFormHandler extends AbstractFormHandler
{
    const STEP_KEY_NAME = 'address';

    /**
     * @inheritdoc
     */
    protected static function getStepKeyName()
    {
        return static::STEP_KEY_NAME;
    }

    /**
     * @return FormInterface
     */
    public function createForm()
    {
        return $this->formFactory->create($this->formClassName, new Address());
    }
}
