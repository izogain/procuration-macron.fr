<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OfficeType extends AbstractType
{
    /**
     * @var string
     */
    protected $officeDataClass;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var string
     */
    protected $userDataClass;

    /**
     * @param string                $officeDataClass
     * @param TokenStorageInterface $tokenStorage
     * @param string                $userDataClass
     */
    public function __construct($officeDataClass, TokenStorageInterface $tokenStorage, $userDataClass)
    {
        $this->officeDataClass = $officeDataClass;
        $this->tokenStorage = $tokenStorage;
        $this->userDataClass = $userDataClass;
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

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent) {
            if (!$user = $this->tokenStorage->getToken()->getUser()) {
                return;
            }

            if (!$user->isSuperAdmin() && !$formEvent->getData()->getReferents()->contains($user)) {
                return;
            }

            $formEvent->getForm()->add('referents', EntityType::class, [
                'class' => $this->userDataClass,
                'query_builder' => function (UserRepository $userRepository) {
                    return $userRepository->queryBuilderAllByName();
                },
                'choice_label' => function (User $user) {
                    return mb_strtoupper($user->getLastName()) . ' ' . ucwords(mb_strtolower($user->getFirstName()));
                },
                'multiple' => true,
                'by_reference' => false,
            ]);
        });
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
