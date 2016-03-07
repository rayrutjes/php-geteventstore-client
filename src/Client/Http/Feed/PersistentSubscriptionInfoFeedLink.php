<?php

namespace RayRutjes\GetEventStore\Client\Http\Feed;

final class PersistentSubscriptionInfoFeedLink extends AbstractFeedLink
{
    const LINK_INFO = 'detail';
    const LINK_REPLAY_PARKED = 'replayParked';

    /**
     * @return array
     */
    protected function getValidRelations(): array
    {
        return [self::LINK_INFO, self::LINK_REPLAY_PARKED];
    }
}
