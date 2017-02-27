<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Procuration;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadProcurationData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * @inheritdoc
     */
    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\ORM\LoadElectionData',
            'AppBundle\DataFixtures\ORM\LoadUserData',
        ];
    }

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $requester = $this->getReference('user-requester');

        $procuration = new Procuration();
        $procuration->setRequester($requester);
        $procuration->setElection($this->getReference('election-0'));

        $manager->persist($procuration);
        $this->addReference('procuration-0', $procuration);

        $pendingProcuration = new Procuration();
        $pendingProcuration->setRequester($requester);
        $pendingProcuration->setElection($this->getReference('election-1'));

        $manager->persist($pendingProcuration);

        $manager->flush();
    }
}
