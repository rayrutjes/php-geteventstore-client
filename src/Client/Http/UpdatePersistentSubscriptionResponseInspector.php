<?php

namespace RayRutjes\GetEventStore\Client\Http;

use Psr\Http\Message\ResponseInterface;

final class UpdatePersistentSubscriptionResponseInspector extends AbstractResponseInspector
{
    /**
     * @param ResponseInterface $response
     *
     * @throws BadRequestException
     */
    public function inspect(ResponseInterface $response)
    {
        $this->filterCommonErrors($response);
        switch ($response->getStatusCode()) {
            /* OK */
            case 200:
                break;

            /* KO. */
            default:
                throw $this->newBadRequestException($response);
        }
    }
}
