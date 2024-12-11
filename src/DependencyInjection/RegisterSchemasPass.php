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

use Neomerx\JsonApi\Contracts\Schema\SchemaInterface;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

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
     * @throws \ReflectionException
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition($this->containerService) && !$container->hasAlias($this->containerService)) {
            return;
        }

        $definition = $container->getDefinition($this->containerService);

        foreach ($container->findTaggedServiceIds($this->schemaTag) as $id => $params) {
            $def = $container->getDefinition($id);

            $class = $def->getClass();

            if (!$r = $container->getReflectionClass($class)) {
                throw new InvalidArgumentException(sprintf(
                    "Class '%s' used for service '%s' cannot be found",
                    $class,
                    $id
                ));
            }

            if (!$r->isSubclassOf(SchemaInterface::class)) {
                throw new \InvalidArgumentException(sprintf(
                    "Service '%s' must implement interface '%s'",
                    $id,
                    SchemaInterface::class
                ));
            }

            if (!isset($params[0]['resource'])) {
                throw new InvalidArgumentException(sprintf(
                    "Service '%s' must define the 'resource' attribute on '%s' tag",
                    $id,
                    $this->schemaTag
                ));
            }

            $definition->addMethodCall(
                'register',
                [$params[0]['resource'], new ServiceClosureArgument(new Reference($id))]
            );
        }
    }
}