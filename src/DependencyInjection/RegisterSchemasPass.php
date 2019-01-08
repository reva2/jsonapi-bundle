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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @package Reva2\JsonApiBundle\DependencyInjection
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class RegisterSchemasPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    protected $containerService;

    /**
     * @var string
     */
    protected $schemaTag;

    public function __construct(
        $containerService = 'reva2_jsonapi.schemas_container',
        $schemaTag = 'reva2_jsonapi.schema'
    ) {
        $this->containerService = $containerService;
        $this->schemaTag = $schemaTag;
    }


    /**
     * @inheritdoc
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->containerService) && !$container->hasAlias($this->containerService)) {
            return;
        }

        $definition = $container->getDefinition($this->containerService);


    }
}