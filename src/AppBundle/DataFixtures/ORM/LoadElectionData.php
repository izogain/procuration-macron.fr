<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Election;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadElectionData implements FixtureInterface
{
    /**
     * @param ObjectManager $manager
     *
     * @return mixed
     */
    public function load(ObjectManager $manager)
    {
        $presidentialFirstRound = new Election();
        $presidentialFirstRound->setLabel('Premier tour de l\'élection présidentielle');
        $presidentialFirstRound->setPerformanceDate(new \DateTime('2017-04-23'));
        $manager->persist($presidentialFirstRound);

        $presidentialSecondRound = new Election();
        $presidentialSecondRound->setLabel('Second tour de l\'élection présidentielle');
        $presidentialSecondRound->setPerformanceDate(new \DateTime('2017-05-07'));
        $manager->persist($presidentialSecondRound);

        $legislativeFirstRound = new Election();
        $legislativeFirstRound->setLabel('Premier tour de l\'élection législative');
        $legislativeFirstRound->setPerformanceDate(new \DateTime('2017-06-11'));
        $manager->persist($legislativeFirstRound);

        $legislativeSecondRound = new Election();
        $legislativeSecondRound->setLabel('Second tour de l\'élection législative');
        $legislativeSecondRound->setPerformanceDate(new \DateTime('2017-06-18'));
        $manager->persist($legislativeSecondRound);

        $fakeElection = new Election();
        $fakeElection->setLabel('Should not be available to users');
        $fakeElection->setPerformanceDate(new \DateTime('2018-01-01'));
        $fakeElection->setActive(false);
        $manager->persist($fakeElection);

        $manager->flush();
    }
}