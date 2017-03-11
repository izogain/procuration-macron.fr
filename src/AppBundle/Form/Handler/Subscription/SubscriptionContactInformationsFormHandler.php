<?php

namespace AppBundle\Form\Handler\Subscription;

class SubscriptionContactInformationsFormHandler extends AbstractFormHandler
{
    const STEP_KEY_NAME = 'contact';

    /**
     * {@inheritdoc}
     */
    protected static function getStepKeyName()
    {
        return static::STEP_KEY_NAME;
    }
}
