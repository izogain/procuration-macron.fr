<?php

namespace AppBundle\Form\Handler\Subscription;

class SubscriptionSearchOfficeHandler extends AbstractFormHandler
{
    const STEP_KEY_NAME = 'office';

    /**
     * @inheritDoc
     */
    protected static function getStepKeyName()
    {
        return static::STEP_KEY_NAME;
    }
}
