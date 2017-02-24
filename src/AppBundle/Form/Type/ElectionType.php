<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElectionType extends AbstractType
{
    /**
     * @var string
     */
    protected $objectClassName;

    /**
     * @param string $objectClassName
     */
    public function __construct($objectClassName)
    {
        $this->objectClassName = $objectClassName;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('label')
            ->add('performanceDate', DateType::class, [
                'years' => $this->generateYearsRange(),
            ])
            ->add('active');
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->objectClassName,
        ]);
    }

    /**
     * @return array
     */
    private function generateYearsRange()
    {
        $currentYear = date('Y');
        $range = range($currentYear, $currentYear + 2);

        return array_combine($range, $range);
    }
}
