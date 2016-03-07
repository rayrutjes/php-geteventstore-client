<?php

namespace RayRutjes\GetEventStore\Client\Http;

use Psr\Http\Message\ResponseInterface;
use RayRutjes\GetEventStore\Client\Http\Feed\EventStreamViaPersistentSubscriptionFeed;
use RayRutjes\GetEventStore\Client\Http\Feed\EventStreamViaPersistentSubscriptionFeedLink;
use RayRutjes\GetEventStore\EventRecord;

final class ReadEventStreamViaPersistentSubscriptionResponseInspector extends AbstractResponseInspector
{
    /**
     * @var EventStreamViaPersistentSubscriptionFeed
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

        $links = [];
        foreach ($data['links'] as $link) {
            $links[] = new EventStreamViaPersistentSubscriptionFeedLink($link['uri'], $link['relation']);
        }

        $events = [];
        foreach ($data['entries'] as $entry) {
            $streamId = $entry['streamId'];

            $number = $entry['eventNumber'];
            $type = $entry['eventType'];
            $eventData = isset($entry['data']) ? $this->decodeData($entry['data']) : [];
            $eventMetadata = isset($entry['metaData']) ? $this->decodeData($entry['metaData']) : [];
            $events[] = new EventRecord($streamId, $number, $type, $eventData, $eventMetadata);
        }

        $isHeadOfStream = $data['headOfStream'];
        $this->feed = new EventStreamViaPersistentSubscriptionFeed($events, $links, $isHeadOfStream);
    }

    /**
     * @return EventStreamViaPersistentSubscriptionFeed
     */
    public function getFeed(): EventStreamViaPersistentSubscriptionFeed
    {
        return $this->feed;
    }
}
