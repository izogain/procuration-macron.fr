<?php

namespace EnMarche\Bundle\CoreBundle\Form\Type;

use EnMarche\Bundle\CoreBundle\Entity\Address;
use EnMarche\Bundle\CoreBundle\Mediator\AddressMediator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('streetNumber', FormType\IntegerType::class, [
                'required' => false,
                'attr' => [
                    'min' => 1,
                ],
                'label' => 'N°',
            ])
            ->add('streetRepeater', FormType\ChoiceType::class, [
                'required' => false,
                'choices' => array_flip(AddressMediator::getStreetRepeaters()),
                'label' => false,
            ])
            ->add('streetType', FormType\ChoiceType::class, [
                'required' => false,
                'choices' => array_flip(AddressMediator::getStreetTypes()),
                'label' => 'Type de voie',
            ])
            ->add('streetName', FormType\TextType::class, [
                'required' => false,
                'label' => 'Nom de voie',
            ])
            ->add('inseeCityCode', FormType\HiddenType::class, [
                'required' => false,
                'error_bubbling' => true,
            ])
            ->add('cityName', TextType::class, [
                'required' => false,
            ])
            ->add('countryCode', UnitedNationsCountryType::class)
        ;

        $field = $builder->create('postalCode', TextType::class, [
            'error_bubbling' => true,
        ]);

        $field->addModelTransformer(new CallbackTransformer(
            function ($data) {
                return $data;
            },
            function ($value) {
                return str_replace(' ', '', $value);
            }
        ));

        $builder->add($field);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'error_bubbling' => false,
        ]);
    }
}
