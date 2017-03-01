<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchOfficeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('city', TextType::class, [
                'label' => 'Ma commune / ma ville d\'expatriation',
                'label_attr' => [
                    'class' => 'form-text',
                ],
                'attr' => [
                    'class' => 'form-input',
                ],
            ])
            ->add('office', ChoiceType::class, [
                'choices' => [],
                'label' => 'Mon bureau de vote / consulat',
                'label_attr' => [
                    'class' => 'form-text',
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
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
