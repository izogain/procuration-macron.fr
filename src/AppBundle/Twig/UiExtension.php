<?php

namespace AppBundle\Twig;

class UiExtension extends \Twig_Extension
{
    const PAGINATION_SELECTED_CLASS = 'form-step--is-active';

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('pagination_class', [$this, 'isPaginationSelected'], ['needs_context' => true]),
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
