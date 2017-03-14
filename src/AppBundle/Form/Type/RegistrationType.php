<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\Subscription\SubscriptionUserInformationsType;
use EnMarche\Bundle\CoreBundle\Form\Type\AddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    /**
     * @var string
     */
    protected $objectClassName;

    /**
     * @var string
     */
    protected $electionRoundObjectClass;

    /**
     * @param string $objectClassName
     * @param string $electionRoundObjectClass
     */
    public function __construct($objectClassName, $electionRoundObjectClass)
    {
        $this->objectClassName = $objectClassName;
        $this->electionRoundObjectClass = $electionRoundObjectClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('address', AddressType::class)
            ->add('votingOffice', OfficeChoiceType::class, [
                'label' => 'Mon bureau de vote',
            ])
            ->add('agreement', CheckboxType::class, [
                'required' => true,
                'mapped' => false,
                'label' => 'En cochant cette case, je m\'engage à voter selon les voeux du mandant.',
            ])
            ->add('elections', ElectionRoundChoiceType::class, [
                'mapped' => false,
            ]);
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
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return SubscriptionUserInformationsType::class;
    }
}
