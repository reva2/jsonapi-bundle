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

use Reva2\JsonApiBundle\DependencyInjection\RegisterEncodersDecodersPass;
use Reva2\JsonApiBundle\DependencyInjection\RegisterSchemasPass;
use Reva2\JsonApiBundle\DependencyInjection\Reva2JsonApiExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Sergey Revenko <dedsemen@gmail.com>
 * @package Reva2\JsonApiBundle
 */
class Reva2JsonApiBundle extends Bundle
{
    /**
     * @inheritdoc
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface
     */
    public function getContainerExtension()
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
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container
            ->addCompilerPass(new RegisterEncodersDecodersPass())
            ->addCompilerPass(new RegisterSchemasPass());
    }
}
