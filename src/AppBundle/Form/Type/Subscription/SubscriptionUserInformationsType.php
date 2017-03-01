<?php

namespace AppBundle\Form\Type\Subscription;

use AppBundle\Mediator\UserMediator;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionUserInformationsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civility', ChoiceType::class, [
                'choices' => array_flip(UserMediator::getCivilities()),
            ])
            ->add('firstName')
            ->add('lastName')
            ->add('birthDate', DateType::class, [
                'years' => $this->generateYears(),
            ])
            ->add('phoneNumber', PhoneNumberType::class, [
                'default_region' => 'FR',
            ])
            ->add('email', EmailType::class)
            ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => false,
        ]);
    }

    /**
     * @return array
     */
    private function generateYears()
    {
        $endYear = date('Y') - 18;
        $range = range($endYear, $endYear - 110);

        return array_combine($range, $range);
    }
}
