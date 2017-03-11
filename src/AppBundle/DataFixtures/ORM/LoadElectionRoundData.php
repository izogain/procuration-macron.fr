<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Election;
use AppBundle\Entity\ElectionRound;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadElectionRoundData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\ORM\LoadElectionData',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var Election $presidentialElection */
        $presidentialElection = $this->getReference('election-pres');
        /** @var Election $legislativeElection */
        $legislativeElection = $this->getReference('election-leg');

        $firstPresidentialRound = new ElectionRound();
        $firstPresidentialRound->setElection($presidentialElection);
        $firstPresidentialRound->setActive(true);
        $firstPresidentialRound->setPerformanceDate(new \DateTime('2017-04-23'));
        $manager->persist($firstPresidentialRound);
        $this->addReference('election-round-pres-1', $firstPresidentialRound);

        $secondPresidentialRound = new ElectionRound();
        $secondPresidentialRound->setElection($presidentialElection);
        $secondPresidentialRound->setActive(true);
        $secondPresidentialRound->setPerformanceDate(new \DateTime('2017-05-07'));
        $manager->persist($secondPresidentialRound);
        $this->addReference('election-round-pres-2', $secondPresidentialRound);

        $firstLegislativeRound = new ElectionRound();
        $firstLegislativeRound->setElection($legislativeElection);
        $firstLegislativeRound->setActive(true);
        $firstLegislativeRound->setPerformanceDate(new \DateTime('2017-06-11'));
        $manager->persist($firstLegislativeRound);
        $this->addReference('election-round-leg-1', $firstLegislativeRound);

        $secondLegislativeRound = new ElectionRound();
        $secondLegislativeRound->setElection($legislativeElection);
        $secondLegislativeRound->setActive(true);
        $secondLegislativeRound->setPerformanceDate(new \DateTime('2017-06-18'));
        $manager->persist($secondLegislativeRound);
        $this->addReference('election-round-leg-2', $secondLegislativeRound);

        $manager->flush();
    }
}
