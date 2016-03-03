<?php

namespace RayRutjes\GetEventStore\Client\Http;

use Psr\Http\Message\ResponseInterface;
use RayRutjes\GetEventStore\Client\Exception\AccessDeniedException;
use RayRutjes\GetEventStore\Client\Exception\BadRequestException;
use RayRutjes\GetEventStore\Client\Exception\StreamDeletedException;

abstract class AbstractResponseInspector implements ResponseInspector
{
    /**
     * @param ResponseInterface $response
     */
    protected function filterCommonErrors(ResponseInterface $response)
    {
        // Globally catch 401 responses, as for now we don't care of the operation context.
        if ($response->getStatusCode() === 401) {
            throw new AccessDeniedException($response->getReasonPhrase(), $response->getStatusCode());
        }

        if ($response->getStatusCode() === 410) {
            throw new StreamDeletedException($response->getReasonPhrase(), $response->getStatusCode());
        }
    }

    /**
     * @param $response
     *
     * @return BadRequestException
     */
    protected function newBadRequestException(ResponseInterface $response)
    {
        $message = sprintf(
            'Unhandled response code %d: %s',
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        return new BadRequestException($message, $response->getStatusCode());
    }

    /**
     * @param ResponseInterface $response
     *
     * @return array
     */
    protected function decodeResponseBody(ResponseInterface $response): array
    {
        return $this->decodeData($response->getBody()->getContents());
    }

    /**
     * @param string $data
     *
     * @return array
     */
    protected function decodeData(string $data): array
    {
        $decoded = json_decode($data, true);

        return !is_array($decoded) ? [] : $decoded;
    }
}
