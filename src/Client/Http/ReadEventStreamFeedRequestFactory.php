<?php

namespace RayRutjes\GetEventStore\Client\Http;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

final class ReadEventStreamFeedRequestFactory implements RequestFactoryInterface
{
    const EMBED = 'embed';
    const EMBED_BODY = 'body';

    /**
     * @var string
     */
    private $uri;

    /**
     * @param string $uri
     */
    public function __construct(string $uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return RequestInterface
     */
    public function buildRequest(): RequestInterface
    {
        return new Request(
            'GET',
            // Add full event entries to the feed.
            $this->uri . '?' . self::EMBED . '=' . self::EMBED_BODY,
            [
                RequestHeader::ACCEPT => ContentType::ATOM_JSON,
            ]
        );
    }
}
