<?php
/*
 * This file is part of the jsonapi-bundle.
 *
 * (c) OrbitSoft LLC <support@orbitsoft.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApiBundle\EventListener;

use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * JSON API exception handler
 *
 * @author Sergey Revenko <sergey.revenko@orbitsoft.com>
 * @package Reva2\JsonApiBundle\EventListener
 */
class ExceptionListener
{
    /**
     * @var EncoderInterface
     */
    protected EncoderInterface $encoder;

    /**
     * Constructor
     *
     * @param EncoderInterface $encoder
     */
    public function __construct(EncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * Converts JSON API exception to response in JSON API format
     *
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof JsonApiException) {
            $response = new Response(
                $this->encoder->encodeErrors($exception->getErrors()),
                $exception->getHttpCode(),
                ['Content-Type' => 'application/vnd.api+json']
            );

            $event->allowCustomResponseCode();
            $event->setResponse($response);
        }
    }
}
