<?php

namespace EnMarche\Bundle\MailjetBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('en_marche_mailjet');

        $root->children()
            ->scalarNode('public_key')->cannotBeEmpty()->isRequired()->end()
            ->scalarNode('private_key')->cannotBeEmpty()->isRequired()->end()
            ->arrayNode('clients')
                ->useAttributeAsKey('client_name')
                ->prototype('array')
                    ->children()
                        ->scalarNode('sender_email')->cannotBeEmpty()->isRequired()->end()
                        ->scalarNode('sender_name')->cannotBeEmpty()->isRequired()->end()
                        ->scalarNode('transport')
                            ->defaultValue('api')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
