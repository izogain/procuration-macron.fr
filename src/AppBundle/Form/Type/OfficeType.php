<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfficeType extends AbstractType
{
    /**
     * @var string
     */
    protected $officeDataClass;

    /**
     * @param string $officeDataClass
     */
    public function __construct($officeDataClass)
    {
        $this->officeDataClass = $officeDataClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('regularOpeningHour', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'min' => 0,
                    'max' => 23,
                ],
                'label' => 'Heure d\'ouverture',
            ])
            ->add('regularClosingHour', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'min' => 0,
                    'max' => 23,
                ],
                'label' => 'Heure de fermeture',
            ])
            ->add('address', AddressType::class, [
                'label' => 'Adresse',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->officeDataClass,
            'translation_domain' => false,
        ]);
    }
}
