<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Address;
use AppBundle\Entity\User;
use AppBundle\Mediator\AddressMediator;
use AppBundle\Mediator\UserMediator;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @inheritdoc
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\ORM\LoadOfficeData',
            'AppBundle\DataFixtures\ORM\LoadElectionData',
        ];
    }

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        /** @var User $superAdmin */
        $superAdmin = $userManager->createUser();
        $superAdmin->setUsername('admin@en-marche.fr');
        $superAdmin->setPlainPassword('admin1234');
        $superAdmin->setSuperAdmin(true);
        $superAdmin->setEnabled(true);
        $superAdmin->setCivility(UserMediator::CIVILITY_MADAM);
        $superAdmin->setFirstName('Super');
        $superAdmin->setLastName('Admin');
        $superAdmin->setBirthDate(new \DateTime('1900-01-01'));
        $superAdmin->setPhoneNumber($this->generatePhoneNumber());
        /** @var \AppBundle\Entity\Address $superAdminAddress */
        $superAdminAddress = $superAdmin->getAddress();
        $superAdminAddress->setStreetType(AddressMediator::STREET_TYPE_STREET);
        $superAdminAddress->setStreetNumber(99);
        $superAdminAddress->setStreetName('Abbé Groult');
        $superAdminAddress->setPostalCode('75015');
        $superAdminAddress->setCity('Paris');
        $superAdminAddress->setCountryCode('FR');
        $superAdmin->setVotingOffice($this->getRandomOfficeInParis15());

        $userManager->updateUser($superAdmin);
        $this->addReference('user-admin', $superAdmin);

        /** @var User $referent */
        $referent = $userManager->createUser();
        $referent->setUsername('referent-ain@en-marche.fr');
        $referent->setPlainPassword('referent1234');
        $referent->setEnabled(true);
        $referent->setCivility(UserMediator::CIVILITY_MISTER);
        $referent->setFirstName('jean-françois');
        $referent->setLastName('dupuis');
        $referent->setBirthDate(new \DateTime('1986-02-29'));
        $referent->setPhoneNumber($this->generatePhoneNumber());
        $referent->setVotingOffice($this->getLyonVotingOffice());
        /** @var \AppBundle\Entity\Address $referentAddress */
        $referentAddress = $referent->getAddress();
        $referentAddress->setStreetName('Clos vert');
        $referentAddress->setPostalCode('01000');
        $referentAddress->setCity('Somewhere');
        $referentAddress->setCountryCode('FR');

        $userManager->updateUser($referent);

        /** @var User $referentRhone */
        $referentRhone = $userManager->createUser();
        $referentRhone->setUsername('referent-rhone@en-marche.fr');
        $referentRhone->setPlainPassword('referent1234');
        $referentRhone->setEnabled(true);
        $referentRhone->setCivility(UserMediator::CIVILITY_MADAM);
        $referentRhone->setFirstName('Jeanine');
        $referentRhone->setLastName('Montrabé');
        $referentRhone->setBirthDate(new \DateTime('1986-02-29'));
        $referentRhone->setPhoneNumber($this->generatePhoneNumber());
        $referentRhone->setVotingOffice($this->getLyonVotingOffice());
        /** @var \AppBundle\Entity\Address $referentRhoneAddress */
        $referentRhoneAddress = $referentRhone->getAddress();
        $referentRhoneAddress->setStreetName('Rue duffour');
        $referentRhoneAddress->setPostalCode('69001');
        $referentRhoneAddress->setCity('Lyon');
        $referentRhoneAddress->setCountryCode('FR');
        $referentRhone->addOfficeInCharge($this->getLyonVotingOffice());

        $userManager->updateUser($referentRhone);
        $this->addReference('user-referent-rhone', $referent);

        /** @var User $requester */
        $requester = $userManager->createUser();
        $requester->setUsername('requester@provider.com');
        $requester->setPlainPassword('requester');
        $requester->setCivility(UserMediator::CIVILITY_MADAM);
        $requester->setFirstName('John');
        $requester->setLastName('Doe');
        $requester->setBirthDate(new \DateTime('1985-09-21'));
        $requester->setPhoneNumber($this->generatePhoneNumber());
        $requester->setVotingOffice($this->getLyonVotingOffice());
        /** @var \AppBundle\Entity\Address $requesterAddress */
        $requesterAddress = $requester->getAddress();
        $requesterAddress->setStreetName('Beauregard');
        $requesterAddress->setPostalCode('69006');
        $requesterAddress->setCity('Lyon');
        $requesterAddress->setCountryCode('FR');

        $userManager->updateUser($requester);
        $this->addReference('user-requester', $requester);

        /** @var User $availableVoter */
        $availableVoter = $userManager->createUser();
        $availableVoter->setUsername('available@en-marche.fr');
        $availableVoter->setPlainPassword('voter1234');
        $availableVoter->setCivility(UserMediator::CIVILITY_MISTER);
        $availableVoter->setFirstName('Vo');
        $availableVoter->setLastName('ter');
        $availableVoter->setBirthDate(new \DateTime('1975-02-24'));
        $availableVoter->setPhoneNumber($this->generatePhoneNumber('0606060606'));
        $availableVoter->setVotingOffice($this->getLyonVotingOffice());
        /** @var \AppBundle\Entity\Address $availableVoterAddress */
        $availableVoterAddress = $availableVoter->getAddress();
        $availableVoterAddress->setStreetNumber(54);
        $availableVoterAddress->setStreetRepeater(AddressMediator::STREET_REPEATER_BIS);
        $availableVoterAddress->setStreetType(AddressMediator::STREET_TYPE_IMPASSE);
        $availableVoterAddress->setStreetName('des bois');
        $availableVoterAddress->setPostalCode('75015');
        $availableVoterAddress->setCity('Paris');
        $availableVoterAddress->setCountryCode('FR');

        $userManager->updateUser($availableVoter);
        $this->addReference('user-voter', $availableVoter);
    }

    /**
     * @return \AppBundle\Entity\Office
     */
    private function getRandomOfficeInParis15()
    {
        return $this->getReference('office-'.mt_rand(0, LoadOfficeData::NB_FIXTURES - 1));
    }

    /**
     * @return \AppBundle\Entity\Office
     */
    private function getLyonVotingOffice()
    {
        return $this->getReference('office-lyon');
    }

    /**
     * @param string $number
     *
     * @return PhoneNumber
     */
    private function generatePhoneNumber($number = '0101010101')
    {
        return (PhoneNumberUtil::getInstance())->parse($number, 'FR');
    }
}
