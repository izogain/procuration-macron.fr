<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Election;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadElectionData extends AbstractFixture
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $presidentialFirstRound = new Election();
        $presidentialFirstRound->setLabel('Premier tour de l\'élection présidentielle');
        $presidentialFirstRound->setPerformanceDate(new \DateTime('2017-04-23'));
        $manager->persist($presidentialFirstRound);
        $this->addReference('election-0', $presidentialFirstRound);

        $presidentialSecondRound = new Election();
        $presidentialSecondRound->setLabel('Second tour de l\'élection présidentielle');
        $presidentialSecondRound->setPerformanceDate(new \DateTime('2017-05-07'));
        $manager->persist($presidentialSecondRound);
        $this->addReference('election-1', $presidentialSecondRound);

        $legislativeFirstRound = new Election();
        $legislativeFirstRound->setLabel('Premier tour de l\'élection législative');
        $legislativeFirstRound->setPerformanceDate(new \DateTime('2017-06-11'));
        $manager->persist($legislativeFirstRound);
        $this->addReference('election-2', $legislativeFirstRound);

        $legislativeSecondRound = new Election();
        $legislativeSecondRound->setLabel('Second tour de l\'élection législative');
        $legislativeSecondRound->setPerformanceDate(new \DateTime('2017-06-18'));
        $manager->persist($legislativeSecondRound);
        $this->addReference('election-3', $legislativeSecondRound);

        $fakeElection = new Election();
        $fakeElection->setLabel('Should not be available to users');
        $fakeElection->setPerformanceDate(new \DateTime('2018-01-01'));
        $fakeElection->setActive(false);
        $manager->persist($fakeElection);
        $this->addReference('election-4', $fakeElection);

        $manager->flush();
    }
}
