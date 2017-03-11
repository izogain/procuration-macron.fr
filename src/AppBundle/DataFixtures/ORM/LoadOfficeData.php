<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Office;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadOfficeData extends AbstractFixture
{
    const NB_FIXTURES = 150;

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < static::NB_FIXTURES; ++$i) {
            $office = new Office();
            $office->setName('Ecole de paris 15 #'.$i);
            $officeAddress = $office->getAddress();
            $officeAddress->setStreetName('Lieux-dit des prés');
            $officeAddress->setPostalCode('75015');
            $officeAddress->setCity('Paris');
            $officeAddress->setCountryCode('FR');

            $manager->persist($office);
            $this->addReference('office-'.$i, $office);
        }

        $lyonOffice = new Office();
        $lyonOffice->setName('Ecole de Lyon 6');
        $lyonOfficeAddress = $lyonOffice->getAddress();
        $lyonOfficeAddress->setStreetName('Beauregard');
        $lyonOfficeAddress->setPostalCode('69006');
        $lyonOfficeAddress->setCity('Lyon');
        $lyonOfficeAddress->setCountryCode('FR');
        $manager->persist($lyonOffice);
        $this->addReference('office-lyon', $lyonOffice);

        $manager->flush();
    }
}
