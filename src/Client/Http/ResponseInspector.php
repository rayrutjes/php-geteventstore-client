<?php

namespace RayRutjes\GetEventStore\Client\Http;

use Psr\Http\Message\ResponseInterface;
use RayRutjes\GetEventStore\Client\Exception\Server\ServerException;

interface ResponseInspector
{
    /**
     * @param ResponseInterface $response
     *
     * @throws ServerException
     */
    public function inspect(ResponseInterface $response);
}
