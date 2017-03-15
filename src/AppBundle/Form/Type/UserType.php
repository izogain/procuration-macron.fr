<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Office;
use AppBundle\Entity\User;
use AppBundle\Repository\OfficeRepository;
use EnMarche\Bundle\CoreBundle\Form\Type\AddressType;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @var string
     */
    protected $objectClassName;

    /**
     * @var string
     */
    protected $officeObjectClassName;

    /**
     * @param string $objectClassName
     * @param string $officeObjectClassName
     */
    public function __construct($objectClassName, $officeObjectClassName)
    {
        $this->objectClassName = $objectClassName;
        $this->officeObjectClassName = $officeObjectClassName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('civility', CivilityChoiceType::class)
            ->add('firstName', null, [
                'label' => 'Prénom',
            ])
            ->add('lastName', null, [
                'label' => 'Nom',
            ])
            ->add('email')
            ->add('birthDate', AppBirthdayType::class)
            ->add('phoneNumber', PhoneNumberType::class, [
                'default_region' => 'FR',
                'label' => 'Téléphone',
            ])
            ->add('address', AddressType::class, [
                'label' => 'Adresse',
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            /** @var \AppBundle\Entity\User $editor */
            $editor = $options['editor'];
            $editorIsSuperadmin = $editor->isSuperAdmin();

            if (!$editorIsSuperadmin && !count($editor->getOfficesInCharge())) {
                return;
            }

            $form = $event->getForm();

            // List only accounts user is in charge of
            if ($this->shareOfficesInCharge($editor, $event->getData())) {
                $form->add('enabled', CheckboxType::class, [
                    'label' => 'Compte actif',
                    'required' => false,
                ]);
            }

//            // List only offices the user can supervise
//            $form->add('votingOffice', SearchOfficeType::class, [
//                'class' => $this->officeObjectClassName,
//                'label' => 'Bureau de vote',
//                'query_builder' => function (OfficeRepository $officeRepository) use ($editor) {
//                    return $this->getOfficesQueryBuilderForEditor($officeRepository, $editor);
//                },
//                'choice_label' => function (Office $office) {
//                    return $this->formatOfficeName($office);
//                },
//            ]);
//
//            // List only offices the user can supervise
//            $form->add('officesInCharge', EntityType::class, [
//                'required' => false,
//                'class' => $this->officeObjectClassName,
//                'multiple' => true,
//                'query_builder' => function (OfficeRepository $officeRepository) use ($editor) {
//                    return $this->getOfficesQueryBuilderForEditor($officeRepository, $editor);
//                },
//                'choice_label' => function (Office $office) {
//                    return $this->formatOfficeName($office);
//                },
//                'label' => 'Bureaux en charge',
//            ]);

            if ($editorIsSuperadmin) {
                $form->add('superAdmin', CheckboxType::class, [
                    'required' => false,
                    'label' => 'Administrateur',
                ]);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->objectClassName,
            'translation_domain' => false,
        ]);

        $resolver->setRequired(['editor']);
        $resolver->setAllowedTypes('editor', $this->objectClassName);
    }

    /**
     * @param User      $editor
     * @param User|null $user
     *
     * @return bool
     */
    private function shareOfficesInCharge(User $editor, User $user = null)
    {
        if ($editor->isSuperAdmin()) {
            return true;
        }

        if (!$user || !$user->getId()) {
            return true;
        }

        $userOfficesInCharge = $user->getOfficesInCharge();

        if (!count($userOfficesInCharge)) {
            return $editor->getOfficesInCharge()->contains($user->getVotingOffice());
        }

        foreach ($editor->getOfficesInCharge() as $editorOfficeInCharge) {
            foreach ($userOfficesInCharge as $userOfficeInCharge) {
                if ($userOfficeInCharge->getId() == $editorOfficeInCharge->getId()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param OfficeRepository $officeRepository
     * @param User             $editor
     *
     * @return \Doctrine\ORM\QueryBuilder|null
     */
    private function getOfficesQueryBuilderForEditor(OfficeRepository $officeRepository, User $editor)
    {
        if ($editor->isSuperAdmin()) {
            return $officeRepository->getQueryBuilderByPostalCode();
        }

        return $officeRepository->getQueryBuilderAllForReferent($editor);
    }

    /**
     * @param Office $office
     *
     * @return string
     */
    private function formatOfficeName(Office $office)
    {
        $officeAddress = $office->getAddress();

        return sprintf('%s %s - %s', $officeAddress->getPostalCode(), $officeAddress->getCity(), $office->getName());
    }
}
