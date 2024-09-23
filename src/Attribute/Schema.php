<?php

namespace Reva2\JsonApiBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Schema
{
    public string $resource;

    /**
     * @param string $resource
     */
    public function __construct(string $resource)
    {
        $this->resource = $resource;
    }
}