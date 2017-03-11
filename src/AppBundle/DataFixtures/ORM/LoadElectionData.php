<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Election;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadElectionData extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $presidential = new Election();
        $presidential->setLabel('présidentielle');
        $manager->persist($presidential);
        $this->addReference('election-pres', $presidential);

        $legislative = new Election();
        $legislative->setLabel('législative');
        $manager->persist($legislative);
        $this->addReference('election-leg', $legislative);

        $manager->flush();
    }
}
