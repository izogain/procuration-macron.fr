<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
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
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $superAdmin = $userManager->createUser();
        $superAdmin->setUsername('xavier@pandawan-technology.com');
        $superAdmin->setPlainPassword('xavier1234');
        $superAdmin->setSuperAdmin(true);
        $superAdmin->setEnabled(true);

        $userManager->updateUser($superAdmin);

        $referent = $userManager->createUser();
        $referent->setUsername('referent@en-marche.fr');
        $referent->setPlainPassword('referent1234');
        $referent->setEnabled(true);
        $referent->addRole('ROLE_ADMIN');

        $userManager->updateUser($referent);
    }
}
