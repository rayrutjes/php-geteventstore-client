<?php

namespace RayRutjes\GetEventStore\Client\Http;

use Psr\Http\Message\ResponseInterface;

final class DeleteStreamResponseInspector extends AbstractResponseInspector
{
    /**
     * @param ResponseInterface $response
     */
    public function inspect(ResponseInterface $response)
    {
        $this->filterCommonErrors($response);
        switch ($response->getStatusCode()) {
            case 204:
                // OK.
                break;
            default:
                // KO.
                throw $this->newBadRequestException($response);
        }
    }
}
