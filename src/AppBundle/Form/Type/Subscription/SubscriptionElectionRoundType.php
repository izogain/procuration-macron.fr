<?php

namespace AppBundle\Form\Type\Subscription;

use AppBundle\Form\Type\ElectionRoundChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SubscriptionElectionRoundType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('election_rounds', ElectionRoundChoiceType::class);
    }
}
