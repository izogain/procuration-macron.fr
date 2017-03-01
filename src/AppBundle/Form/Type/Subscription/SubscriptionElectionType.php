<?php

namespace AppBundle\Form\Type\Subscription;

use AppBundle\Repository\ElectionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SubscriptionElectionType extends AbstractType
{
    /**
     * @var string
     */
    protected $electionDataClass;

    /**
     * @param string $electionDataClass
     */
    public function __construct($electionDataClass)
    {
        $this->electionDataClass = $electionDataClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('elections', EntityType::class, [
            'class' => $this->electionDataClass,
            'expanded' => true,
            'multiple' => true,
            'query_builder' => function (ElectionRepository $repository) {
                return $repository->queryAllEnabledByDateAsc();
            }
        ]);
    }
}
