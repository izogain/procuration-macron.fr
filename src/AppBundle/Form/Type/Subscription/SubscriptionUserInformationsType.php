<?php

namespace AppBundle\Form\Type\Subscription;

use AppBundle\Form\Type\AppBirthdayType;
use AppBundle\Form\Type\CivilityChoiceType;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('civility', CivilityChoiceType::class)
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('birthDate', AppBirthdayType::class)
            ->add('phoneNumber', PhoneNumberType::class, [
                'default_region' => 'FR',
                'label' => 'Téléphone',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
            ]);
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
}
