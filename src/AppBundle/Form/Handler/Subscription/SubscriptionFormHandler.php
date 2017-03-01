<?php

namespace AppBundle\Form\Handler\Subscription;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SubscriptionFormHandler
{
    const STORAGE_KEY_NAME = '_sub';

    /**
     * @param SessionInterface $session
     */
    public function resetStoredData(SessionInterface $session)
    {
        $session->remove(static::STORAGE_KEY_NAME);
    }
}
