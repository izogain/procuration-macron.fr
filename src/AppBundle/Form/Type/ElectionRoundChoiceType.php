<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\ElectionRound;
use AppBundle\Repository\ElectionRoundRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;

class ElectionRoundChoiceType extends AbstractType
{
    /**
     * @var string
     */
    protected $electionRoundObjectClass;

    /**
     * @param string $electionRoundObjectClass
     */
    public function __construct($electionRoundObjectClass)
    {
        $this->electionRoundObjectClass = $electionRoundObjectClass;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => $this->electionRoundObjectClass,
            'expanded' => true,
            'multiple' => true,
            'query_builder' => function (ElectionRoundRepository $repository) {
                return $repository->queryAllUpcomingEnabledByDateAsc();
            },
            'choice_label' => function (ElectionRound $electionRound) {
                return 'Election '. $electionRound->getElection()->getLabel();
            },
            'required' => true,
            'constraints' => [
                new Count([
                    'min' => 1,
                    'minMessage' => 'Vous devez sélectionner au moins une élection',
                ]),
            ],
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
