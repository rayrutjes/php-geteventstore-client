<?php

namespace RayRutjes\GetEventStore\Client\Http;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

final class ReadEventStreamViaPersistentSubscriptionRequestFactory implements RequestFactoryInterface
{
    /**
     * @var int
     */
    private $batchSize;

    /**
     * @var string
     */
    private $uri;

    /**
     * @param string   $uri
     * @param int|null $batchSize
     */
    public function __construct(string $uri, int $batchSize = 1)
    {
        $this->uri = $uri;
        $this->batchSize = $batchSize;
    }

    /**
     * @return RequestInterface
     */
    public function buildRequest(): RequestInterface
    {
        $batchSizePart = '/' . (string) $this->batchSize;

        $path = $this->uri;
        // if path does not already ends with the batchSize part, add it.
        if (($temp = strlen($path) - strlen($batchSizePart)) < 0 || strpos($path, $batchSizePart, $temp) === false) {
            $path .= $batchSizePart;
        }
        $path .= '?' . ReadEventStreamFeedRequestFactory::EMBED . '=' . ReadEventStreamFeedRequestFactory::EMBED_BODY;

        return new Request(
            'GET',
            $path,
            [
                RequestHeader::ACCEPT => ContentType::COMPETING_ATOM_JSON,
            ]
        );
    }
}
