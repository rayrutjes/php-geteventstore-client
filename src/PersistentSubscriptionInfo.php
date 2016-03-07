<?php

namespace RayRutjes\GetEventStore;

final class PersistentSubscriptionInfo
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
     * @return StreamId
     */
    public function getStreamId(): StreamId
    {
        return $this->streamId;
    }

    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->groupName;
    }

    /**
     * Returns a clone of the settings for immutability.
     *
     * @return PersistentSubscriptionSettings
     */
    public function getSettings(): PersistentSubscriptionSettings
    {
        return clone $this->settings;
    }
}
