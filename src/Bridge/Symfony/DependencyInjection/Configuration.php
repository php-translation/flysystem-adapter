<?php

/*
 * This file is part of the PHP Translation package.
 *
 * (c) PHP Translation team <tobias.nyholm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Translation\PlatformAdapter\Flysystem\Bridge\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('translation_adapter_flysystem');

        $root->children()
            ->arrayNode('filesystems')
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->children()
                ->scalarNode('flysystem_service')->isRequired()->end()
                ->scalarNode('path')->isRequired()->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
