<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\VotingAvailability;
use AppBundle\Repository\VotingAvailabilityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProcurationAssignationType extends AbstractType
{
    /**
     * @var string
     */
    protected $procurationEntityDataClass;

    /**
     * @var string
     */
    protected $votingAvailabillityEntityDataClass;

    /**
     * @param string $procurationEntityDataClass
     * @param        $votingAvailabillityEntityDataClass
     */
    public function __construct(
        $procurationEntityDataClass,
        $votingAvailabillityEntityDataClass
    ) {
        $this->procurationEntityDataClass = $procurationEntityDataClass;
        $this->votingAvailabillityEntityDataClass = $votingAvailabillityEntityDataClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \AppBundle\Entity\Procuration $procuration */
        $procuration = $builder->getData();
        $election = $procuration->getElection();
        // TODO use country code when city is not in France
        $voterOfficeAddress = $procuration->getRequester()->getVotingOffice()->getAddress();
        $countryCode = $voterOfficeAddress->getCountryCode();
        $cityPostalCode = $voterOfficeAddress->getPostalCode();

        $builder->add('votingAvailability', EntityType::class, [
            'class' => $options['voting_availability_data_class'],
            'query_builder' => function(VotingAvailabilityRepository $votingAvailabilityRepository) use ($election, $countryCode, $cityPostalCode) {
                return $votingAvailabilityRepository->getQueryBuilderForAvailableForElectionInArea($election->getId(), $countryCode, $cityPostalCode);
            },
            'choice_label' => function(VotingAvailability $votingAvailability) {
                $voter = $votingAvailability->getVoter();

                return sprintf('%s — %s (%s)', $voter->getVotingOffice()->getName(), $voter, $voter->getPhoneNumber());
            }
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->procurationEntityDataClass,
            'voting_availability_data_class' => $this->votingAvailabillityEntityDataClass,
        ]);
    }
}
