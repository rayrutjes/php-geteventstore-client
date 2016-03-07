<?php

namespace RayRutjes\GetEventStore\Client\Http;

use Psr\Http\Message\ResponseInterface;
use RayRutjes\GetEventStore\Client\Http\Feed\PersistentSubscriptionInfoFeed;
use RayRutjes\GetEventStore\Client\Http\Feed\PersistentSubscriptionInfoFeedLink;
use RayRutjes\GetEventStore\PersistentSubscriptionInfo;
use RayRutjes\GetEventStore\PersistentSubscriptionSettings;
use RayRutjes\GetEventStore\StreamId;

final class GetPersistentSubscriptionInfoResponseInspector extends AbstractResponseInspector
{
    /**
     * @var PersistentSubscriptionInfoFeed
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
            $links[] = new PersistentSubscriptionInfoFeedLink($link['href'], $link['rel']);
        }

        $settings = new PersistentSubscriptionSettings();
        $config = $data['config'];

        if ($config['resolveLinktos']) {
            $settings->resolveLinktos();
        } else {
            $settings->doNotResolveLinktos();
        }
        $settings->startFrom($config['startFrom']);
        $settings->withMessageTimeoutInMillisecondsOf($config['messageTimeoutMilliseconds']);
        if ($config['extraStatistics']) {
            $settings->withExtraStatistics();
        }
        $settings->withMaxRetriesOf($config['maxRetryCount']);
        $settings->WithReadBatchOf($config['readBatchSize']);

        // todo: not sure of how preferRoundRobin relates to namedConsumerStrategy.
        if ($config['preferRoundRobin'] || $config['namedConsumerStrategy'] == PersistentSubscriptionSettings::STRATEGY_ROUND_ROBIN) {
            $settings->preferRoundRobin();
        } else {
            $settings->preferDispatchToSingle();
        }
        $settings->checkPointAfter($config['checkPointAfterMilliseconds']);
        $settings->minCheckPointOf($config['minCheckPointCount']);
        $settings->maxCheckPointOf($config['maxCheckPointCount']);
        $settings->withMaxSubscribersOf($config['maxSubscriberCount']);

        $streamId = new StreamId($data['eventStreamId']);
        $groupName = $data['groupName'];

        $info = new PersistentSubscriptionInfo($streamId, $groupName, $settings);

        $this->feed = new PersistentSubscriptionInfoFeed($links, $info);
    }

    /**
     * @return PersistentSubscriptionInfoFeed
     */
    public function getFeed(): PersistentSubscriptionInfoFeed
    {
        return $this->feed;
    }
}
