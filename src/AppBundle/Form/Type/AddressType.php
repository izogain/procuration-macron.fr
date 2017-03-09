<?php

namespace AppBundle\Form\Type;

use AppBundle\Mediator\AddressMediator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    /**
     * @var string
     */
    protected $dataClassName;

    /**
     * @param string $dataClassName
     */
    public function __construct(string $dataClassName)
    {
        $this->dataClassName = $dataClassName;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('streetNumber', FormType\IntegerType::class, [
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
                'label' => 'Nom de la voie',
            ])
//            ->add('extraInformations', FormType\TextType::class, ['required' => false])
            ->add('postalCode', FormType\TextType::class, [
                'label' => 'Code postal',
            ])
            ->add('city', FormType\TextType::class, [
                'label' => 'Ville'
            ])
            ->add('countryCode', FormType\CountryType::class, [
                'label' => 'Pays'
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->dataClassName,
            'translation_domain' => false,
        ]);
    }
}
