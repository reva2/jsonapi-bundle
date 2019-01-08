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

use Reva2\JsonApi\Contracts\Decoders\CallbackResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Callback resolver
 *
 * @package Reva2\JsonApiBundle
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class CallbackResolver implements CallbackResolverInterface
{
    const SERVICE_PATTERN = "/[A-Za-z0-9\._\-]+:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/";
    const STATIC_METHOD_PATTERN = "/[A-Za-z0-9\._\-]+::[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/";

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     * @param string $name
     * @return callable
     * @throws \Exception
     */
    public function resolveCallback($name)
    {
        $callback = $name;
        if (preg_match(static::SERVICE_PATTERN, $name)) {
            list($service, $method) = explode(':', $name, 2);

            if (isset($this->container[$service])) {
                $callback = [$this->container->get($service), $method];
            }
        } elseif (preg_match(static::STATIC_METHOD_PATTERN, $name)) {
            $callback = explode('::', $name, 2);
        }

        if (!is_callable($callback)) {
            throw new \InvalidArgumentException(sprintf("'%s' is not callable", $name));
        }

        return $callback;
    }
}