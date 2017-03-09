<?php

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfficeChoiceType extends AbstractType
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => $this->officeDataClass,
            'multiple' => false,
            'expanded' => false,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return EntityType::class;
    }
}
