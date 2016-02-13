<?php

namespace RayRutjes\GetEventStore\Client\Http;

use Psr\Http\Message\ResponseInterface;
use RayRutjes\GetEventStore\Client\Exception\WrongExpectedVersionException;

class AppendToStreamResponseInspector extends AbstractResponseInspector
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
            case 201:
                break;

            /*
             * The ES Api will try to redirect us if we do not provide an eventId.
             * Actually this client is designed to avoid that scenario.
             * The httpClient does not allow redirects anyway.
             * See: http://docs.geteventstore.com/http-api/3.4.0/writing-to-a-stream/#expected-version
             */
            case 301:
                throw new \LogicException('Please help us understand how you got here!!!');

            /* Catch known error, otherwise fall-through to a more generic exception. */
            case 400:
                if ($response->getReasonPhrase() == 'Wrong expected EventNumber') {
                    throw new WrongExpectedVersionException();
                }

            /* KO. */
            default:
                throw $this->newBadRequestException($response);
        }
    }
}
