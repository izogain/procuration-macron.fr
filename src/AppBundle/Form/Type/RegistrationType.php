<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\ElectionRound;
use AppBundle\Form\Type\Subscription\SubscriptionUserInformationsType;
use AppBundle\Repository\ElectionRepository;
use AppBundle\Repository\ElectionRoundRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;

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
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('address', AddressType::class)
            ->add('elections', ElectionRoundChoiceType::class, [
                'mapped' => false,
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->objectClassName,
            'translation_domain' => false,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return SubscriptionUserInformationsType::class;
    }
}
