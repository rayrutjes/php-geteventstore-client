<?php

namespace RayRutjes\GetEventStore\Client\Http;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use RayRutjes\GetEventStore\PersistentSubscriptionSettings;
use RayRutjes\GetEventStore\StreamId;

final class UpdatePersistentSubscriptionRequestFactory implements RequestFactoryInterface
{
    /**
     * @var StreamId
     */
    private $streamId;

    /**
     * @var string
     */
    private $groupName;

    /**
     * @var PersistentSubscriptionSettings
     */
    private $settings;

    /**
     * @param StreamId                       $streamId
     * @param string                         $groupName
     * @param PersistentSubscriptionSettings $settings
     */
    public function __construct(StreamId $streamId, string $groupName, PersistentSubscriptionSettings $settings)
    {
        $this->streamId = $streamId;
        $this->groupName = $groupName;
        $this->settings = $settings;
    }

    /**
     * @return RequestInterface
     */
    public function buildRequest(): RequestInterface
    {
        return new Request(
            'POST',
            sprintf('subscriptions/%s/%s', $this->streamId->toString(), $this->groupName),
            [
                RequestHeader::CONTENT_TYPE => ContentType::JSON,
            ],
            json_encode($this->settings)
        );
    }
}
