<?php

declare(strict_types=1);

namespace Serafort\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('serafort');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('endpoint')->defaultValue('https://api.serafort.com')->end()
                ->scalarNode('client_id')->defaultNull()->end()
                ->scalarNode('client_secret')->defaultNull()->end()
                ->integerNode('leeway')->defaultValue(60)->end()
            ->end();

        return $treeBuilder;
    }
}
