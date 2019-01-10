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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @package Reva2\JsonApiBundle\DependencyInjection
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class Reva2JsonApiExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $locator = new FileLocator(__DIR__ .'/../Resources/config');
        $loader = new XmlFileLoader($container, $locator);

        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $this->registerCache($config, $container);
        $this->configureListener($config, $container);
        $this->cofigureSchemaContainer($config, $container);
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getAlias()
    {
        return 'reva2_jsonapi';
    }

    /**
     * Register jsonapi cache
     *
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function registerCache(array $config, ContainerBuilder $container)
    {
        switch ($config['cache_adapter']) {
            case 'filesystem':
                $adapterDef = $container->getDefinition('reva2_jsonapi.cache_adapter_filesystem');
                $adapterDef->setArgument(0, $config['cache_dir']);
                break;

            case 'void':
                $adapterDef = $container->getDefinition('reva2_jsonapi.cache_adapter_void');
                break;

            default:
                throw new \InvalidArgumentException(sprintf(
                    "Invalid cache adapter '%s' specified",
                    $config['cache_adapter']
                ));
        }

        $container->setDefinition('reva2_jsonapi.cache', $adapterDef);
    }

    /**
     * Configure event listener that build request environment
     *
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function configureListener(array $config, ContainerBuilder $container)
    {
        $definition = $container->getDefinition('reva2_jsonapi.listener');
        $definition->setArgument(
            2,
            [
                'decoders' => $config['decoders'],
                'encoders' => $config['encoders']
            ]
        );
    }

    private function cofigureSchemaContainer(array $config, ContainerBuilder $container)
    {
        $definition = $container->getDefinition('reva2_jsonapi.schemas_container');
        $definition->setArgument(1, $config['schemas']);
    }
}
