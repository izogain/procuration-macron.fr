<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Procuration;
use AppBundle\Mediator\ProcurationMediator;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadProcurationData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\ORM\LoadElectionRoundData',
            'AppBundle\DataFixtures\ORM\LoadUserData',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $requester = $this->getReference('user-requester');

        $procuration = new Procuration();
        $procuration->setRequester($requester);
        $procuration->setElectionRound($this->getReference('election-round-pres-1'));
        $procuration->setReason(static::getRandomReason());

        $manager->persist($procuration);
        $this->addReference('procuration-0', $procuration);

        $pendingProcuration = new Procuration();
        $pendingProcuration->setRequester($requester);
        $pendingProcuration->setElectionRound($this->getReference('election-round-pres-2'));
        $pendingProcuration->setReason(static::getRandomReason());

        $manager->persist($pendingProcuration);

        $manager->flush();
    }

    /**
     * @return int
     */
    private static function getRandomReason()
    {
        return mt_rand(0, count(ProcurationMediator::getReasons()) - 1);
    }
}
