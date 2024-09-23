<?php
/*
 * This file is part of the jsonapi-bundle.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApiBundle;

use ReflectionClass;
use Reva2\JsonApiBundle\Attribute\Schema;
use Reva2\JsonApiBundle\DependencyInjection\RegisterEncodersDecodersPass;
use Reva2\JsonApiBundle\DependencyInjection\RegisterSchemasPass;
use Reva2\JsonApiBundle\DependencyInjection\Reva2JsonApiExtension;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Sergey Revenko <dedsemen@gmail.com>
 * @package Reva2\JsonApiBundle
 */
class Reva2JsonApiBundle extends Bundle
{
    /**
     * @inheritdoc
     * @return ExtensionInterface
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new Reva2JsonApiExtension();
        }

        return $this->extension;
    }

    /**
     * @inheritdoc
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->registerAttributeForAutoconfiguration(
            Schema::class,
            static function (ChildDefinition $definition, Schema $attribute, ReflectionClass $reflector): void {
                $definition->addTag('reva2_jsonapi.schema', ['resource' => $attribute->resource]);
            }
        );

        $container
            ->addCompilerPass(new RegisterEncodersDecodersPass())
            ->addCompilerPass(new RegisterSchemasPass());
    }
}
