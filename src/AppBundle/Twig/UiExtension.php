<?php

namespace AppBundle\Twig;

use AppBundle\Mediator\UserMediator;

class UiExtension extends \Twig_Extension
{
    const PAGINATION_SELECTED_CLASS = 'form-step--is-active';

    /**
     * @var UserMediator
     */
    protected $userMediator;

    /**
     * @param UserMediator $userMediator
     */
    public function __construct(UserMediator $userMediator)
    {
        $this->userMediator = $userMediator;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('pagination_class', [$this, 'isPaginationSelected'], ['needs_context' => true]),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('gender', [$this->userMediator, 'getCivility']),
        ];
    }

    /**
     * @param array $context
     * @param int   $attemptStep
     *
     * @return string
     */
    public function isPaginationSelected($context, $attemptStep)
    {
        if (!isset($context['pagination_step']) && $attemptStep != 1) {
            return '';
        }

        if (!isset($context['pagination_step']) && $attemptStep == 1) {
            return static::PAGINATION_SELECTED_CLASS;
        }

        return $context['pagination_step'] == $attemptStep ? static::PAGINATION_SELECTED_CLASS : '';
    }
}
