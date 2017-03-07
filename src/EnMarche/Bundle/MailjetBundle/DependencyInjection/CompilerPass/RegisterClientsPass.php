<?php

namespace EnMarche\Bundle\MailjetBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterClientsPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $clientsConfiguration = $container->getParameter('en_marche_mailjet.clients_configuration');

        $this->loadMessageFactories($container, $clientsConfiguration);
        $this->loadClients($container, $clientsConfiguration);
    }


    /**
     * @param ContainerBuilder $containerBuilder
     * @param array            $clientConfigurations
     */
    private function loadMessageFactories(ContainerBuilder $containerBuilder, array $clientConfigurations)
    {
        foreach ($clientConfigurations as $clientName => $clientConfiguration) {
            $messageFactoryDefinition = new Definition('EnMarche\\Bundle\\MailjetBundle\\Factory\\MailjetTemplateEmailFactory');
            $messageFactoryDefinition->setArguments([
                $clientConfiguration['sender_email'],
                $clientConfiguration['sender_name']
            ]);

            $containerBuilder->setDefinition('en_marche_mailjet.message_factory.'.$clientName, $messageFactoryDefinition);
        }
    }
    /**
     * @param ContainerBuilder $containerBuilder
     * @param array            $clientConfigurations
     */
    private function loadClients(ContainerBuilder $containerBuilder, array $clientConfigurations)
    {
        foreach ($clientConfigurations as $clientName => $clientConfig) {
            $clientDefinition = new Definition('EnMarche\\Bundle\\MailjetBundle\\Client\\MailjetClient');
            $clientDefinition->setArguments([
                $containerBuilder->findDefinition('event_dispatcher'),
                $containerBuilder->findDefinition('en_marche_mailjet.transport.'.$clientConfig['transport']),
                $containerBuilder->findDefinition('en_marche_mailjet.message_factory.'.$clientName),
            ]);

            $containerBuilder->setDefinition('en_marche_mailjet.client.'.$clientName, $clientDefinition);
        }
    }
}
