<?php

namespace AppBundle\Form\Type;

use AppBundle\Repository\OfficeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchOfficeType extends AbstractType
{
    /**
     * @var string
     */
    protected $officeRepository;

    /**
     * @var string
     */
    protected $officeObjectClass;

    /**
     * @param OfficeRepository $officeRepository
     */
    public function __construct(OfficeRepository $officeRepository)
    {
        $this->officeRepository = $officeRepository;
        $this->officeObjectClass = $officeRepository->getClassName();
    }

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
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $this->attachOfficeWidget($event, []);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $this->attachOfficeWidget(
                $event,
                [
                    $this->officeRepository->find($event->getData()['office']),
                ]
            );
        });
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
     * Attach the "office" widget with the proper choices.
     *
     * @param FormEvent $event
     * @param array     $choices
     */
    private function attachOfficeWidget(FormEvent $event, array $choices)
    {
        $event->getForm()->add('office', EntityType::class, [
            'class' => $this->officeObjectClass,
            'choices' => $choices,
            'label' => 'Mon bureau de vote / consulat',
            'label_attr' => [
                'class' => 'form-text',
            ],
            'attr' => [
                'class' => 'form-select',
            ],
        ]);
    }
}
