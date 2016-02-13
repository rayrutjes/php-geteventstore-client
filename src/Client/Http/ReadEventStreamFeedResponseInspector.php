<?php

namespace RayRutjes\GetEventStore\Client\Http;

use Psr\Http\Message\ResponseInterface;
use RayRutjes\GetEventStore\EventRecord;
use RayRutjes\GetEventStore\Client\Http\Feed\EventStreamFeed;
use RayRutjes\GetEventStore\Client\Http\Feed\EventStreamFeedLink;

class ReadEventStreamFeedResponseInspector extends AbstractResponseInspector
{
    /**
     * @var EventStreamFeed
     */
    private $feed;

    /**
     * @param ResponseInterface $response
     */
    public function inspect(ResponseInterface $response)
    {
        $this->filterCommonErrors($response);
        switch ($response->getStatusCode()) {
            case 200:
                // OK.
                break;
            default:
                // KO.
                throw $this->newBadRequestException($response);
        }
        $data = $this->decodeResponseBody($response);

        // Todo: Handle parsing exceptions and throw corresponding errors.
        $links = [];
        foreach ($data['links'] as $link) {
            $links[] = new EventStreamFeedLink($link['uri'], $link['relation']);
        }

        $events = [];
        foreach ($data['entries'] as $entry) {
            $streamId = $entry['streamId'];

            $number = $entry['eventNumber'];
            $type = $entry['eventType'];
            $eventData = isset($entry['data']) ? $this->decodeData($entry['data']) : [];
            $eventMetadata = isset($entry['metadata']) ? $this->decodeData($entry['metadata']) : [];
            // todo: figure out why metadata is always empty.

            $events[] = new EventRecord($streamId, $number, $type, $eventData, $eventMetadata);
        }

        $isHeadOfStream = $data['headOfStream'];
        $eTag = $data['eTag'] ?? null;
        $this->feed = new EventStreamFeed($events, $links, $isHeadOfStream, $eTag);
    }

    /**
     * @return EventStreamFeed
     */
    public function getFeed(): EventStreamFeed
    {
        return $this->feed;
    }
}
