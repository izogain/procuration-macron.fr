<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('regularOpeningHour', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'min' => 0,
                    'max' => 23,
                ]
            ])
            ->add('regularClosingHour', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'min' => 0,
                    'max' => 23,
                ]
            ])
            ->add('address', AddressType::class);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->officeDataClass,
            'translation_domain' => false,
        ]);
    }
}
