<?php

namespace RayRutjes\GetEventStore\Client\Http\Feed;

final class EventStreamViaPersistentSubscriptionFeedLink extends AbstractFeedLink
{
    const LINK_SELF = 'self';
    const LINK_FIRST = 'first';
    const LINK_LAST = 'last';
    const LINK_PREVIOUS = 'previous';
    const LINK_NEXT = 'next';
    const LINK_ACK_ALL = 'ackAll';
    const LINK_NACK_ALL = 'nackAll';

    /**
     * @return array
     */
    protected function getValidRelations(): array
    {
        return [
            self::LINK_SELF,
            self::LINK_FIRST,
            self::LINK_LAST,
            self::LINK_PREVIOUS,
            self::LINK_NEXT,
            self::LINK_ACK_ALL,
            self::LINK_NACK_ALL,
        ];
    }
}
