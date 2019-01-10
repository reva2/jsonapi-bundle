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

use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Reva2\JsonApi\Contracts\Decoders\DecoderInterface;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Reva2\JsonApiBundle\DependencyInjection
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class RegisterEncodersDecodersPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    protected $registryService;

    /**
     * @var string
     */
    protected $encodersTag;

    /**
     * @var string
     */
    protected $decodersTag;

    /**
     * @param string $registryService
     * @param string $encodersTag
     * @param string $decodersTag
     */
    public function __construct(
        $registryService = 'reva2_jsonapi.registry',
        $encodersTag = 'reva2_jsonapi.encoder',
        $decodersTag = 'reva2_jsonapi.decoder'
    ) {
        $this->registryService = $registryService;
        $this->encodersTag = $encodersTag;
        $this->decodersTag = $decodersTag;
    }

    /**
     * @inheritdoc
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->registryService) && !$container->hasAlias($this->registryService)) {
            return;
        }

        $definition = $container->getDefinition($this->registryService);

        $this->registerEncoders($definition, $container);
        $this->registerDecoders($definition, $container);
    }

    /**
     * Register response encoders
     *
     * @param Definition $definition
     * @param ContainerBuilder $container
     * @throws \ReflectionException
     */
    private function registerEncoders(Definition $definition, ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds($this->encodersTag, true) as  $id => $params ) {

            $def = $container->getDefinition($id);

            $class = $def->getClass();

            if (!$r = $container->getReflectionClass($class)) {
                throw new InvalidArgumentException(sprintf(
                    "Class '%s' used for service '%s' cannot be found",
                    $class,
                    $id
                ));
            }

            if (!$r->isSubclassOf(EncoderInterface::class)) {
                throw new \InvalidArgumentException(sprintf(
                    "Service '%s' must implement interface '%s'",
                    $id,
                    EncoderInterface::class
                ));
            }

            if (!isset($params[0]['alias'])) {
                throw new InvalidArgumentException(sprintf(
                    "Service '%s' must define the 'alias' attribute on '%s' tag",
                    $id,
                    $this->encodersTag
                ));
            }

            $definition->addMethodCall(
                'registerEncoder',
                [$params[0]['alias'], new ServiceClosureArgument(new Reference($id))]
            );
        }
    }

    /**
     * Register request decoders
     *
     * @param Definition $definition
     * @param ContainerBuilder $container
     * @throws \ReflectionException
     */
    private function registerDecoders(Definition $definition, ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds($this->decodersTag, true) as $id => $params) {
            $def = $container->getDefinition($id);

            $class = $def->getClass();

            if (!$r = $container->getReflectionClass($class)) {
                throw new InvalidArgumentException(sprintf(
                    "Class '%s' used for service '%s' cannot be found",
                    $class,
                    $id
                ));
            }

            if (!$r->isSubclassOf(DecoderInterface::class)) {
                throw new \InvalidArgumentException(sprintf(
                    "Service '%s' must implement interface '%s'",
                    $id,
                    EncoderInterface::class
                ));
            }

            if (!isset($params[0]['alias'])) {
                throw new InvalidArgumentException(sprintf(
                    "Service '%s' must define the 'alias' attribute on '%s' tag",
                    $id,
                    $this->encodersTag
                ));
            }

            $definition->addMethodCall(
                'registerDecoder',
                [$params[0]['alias'], new ServiceClosureArgument(new Reference($id))]
            );
        }
    }
}