<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElectionRoundType extends AbstractType
{
    /**
     * @var string
     */
    protected $electionRoundDataClass;

    /**
     * @param string $electionRoundDataClass
     */
    public function __construct($electionRoundDataClass)
    {
        $this->electionRoundDataClass = $electionRoundDataClass;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('election', null, [
                'required' => true,
            ])
            ->add('performanceDate', DateType::class, [
                'label' => 'Date'
            ])
            ->add('active', CheckboxType::class);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->electionRoundDataClass,
            'translation_domain' => false,
        ]);
    }
}
