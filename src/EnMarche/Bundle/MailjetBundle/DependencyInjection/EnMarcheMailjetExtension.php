<?php

namespace EnMarche\Bundle\MailjetBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class EnMarcheMailjetExtension extends Extension
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('en_marche_mailjet.public_key', $config['public_key']);
        $container->setParameter('en_marche_mailjet.private_key', $config['private_key']);
        $container->setParameter('en_marche_mailjet.clients_configuration', $config['clients']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('transport.xml');
        $loader->load('event_subscriber.xml');
        $loader->load('repository.xml');
    }
}
