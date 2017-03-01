<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\VotingAvailability;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadVotingAvailabilityData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * @inheritdoc
     */
    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\ORM\LoadUserData',
            'AppBundle\DataFixtures\ORM\LoadElectionData',
            'AppBundle\DataFixtures\ORM\LoadProcurationData',
        ];
    }

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $votingAvailability = new VotingAvailability();
        $votingAvailability->setVoter($this->getVoterInLyon());
        $votingAvailability->setElection($this->getReference('election-0'));

        $manager->persist($votingAvailability);

        $votingAvailability = new VotingAvailability();
        $votingAvailability->setVoter($this->getVoterInLyon());
        $votingAvailability->setElection($this->getReference('election-3'));

        $manager->persist($votingAvailability);

        /** @var \AppBundle\Entity\User $userAdmin */
        $userAdmin = $this->getReference('user-admin');

        for ($i = 0; $i < 5; ++$i) {
            $votingAvailability = new VotingAvailability();
            $votingAvailability->setVoter($userAdmin);
            $votingAvailability->setElection($this->getReference('election-'.$i));

            $manager->persist($votingAvailability);
        }

        $manager->flush();
    }

    /**
     * @return \AppBundle\Entity\User
     */
    private function getVoterInLyon()
    {
        return $this->getReference('user-voter');
    }
}
