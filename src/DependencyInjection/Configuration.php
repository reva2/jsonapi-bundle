<?php
/*
 * This file is part of the jsonapi-bundle.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('reva2_jsonapi');
        $rootNode
            ->children()
                ->enumNode('cache_adapter')
                    ->info('Adapter for metadata cache')
                    ->values(['filesystem', 'void'])
                    ->defaultValue('filesystem')
                    ->end()
                ->scalarNode('cache_dir')
                    ->info('Path to cache directory for filesystem metadata cache adapter')
                    ->defaultValue('%kernel.cache_dir%/reva2/jsonapi')
                    ->end()
                ->variableNode('schemas')
                    ->info('JSON API schemas')
                    ->defaultValue([])
                    ->end()
                ->variableNode('decoders')
                    ->info('Decoders mapping')
                    ->defaultValue([
                        'application/vnd.api+json' => 'jsonapi',
                        'application/json' => 'jsonapi'
                    ])
                    ->end()
                ->variableNode('encoders')
                    ->info('Encoders mapping')
                    ->defaultValue([
                        'application/vnd.api+json' => 'jsonapi',
                        'application/json' => 'jsonapi'
                    ])
                    ->end();

        return $treeBuilder;
    }
}