<?php

namespace RayRutjes\GetEventStore\Client\Http;

use Psr\Http\Message\RequestInterface;

interface RequestFactoryInterface
{
    /**
     * @return RequestInterface
     */
    public function buildRequest(): RequestInterface;
}
