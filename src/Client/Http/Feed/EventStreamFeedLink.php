<?php

namespace RayRutjes\GetEventStore\Client\Http\Feed;

final class EventStreamFeedLink extends AbstractFeedLink
{
    const LINK_SELF = 'self';
    const LINK_FIRST = 'first';
    const LINK_LAST = 'last';
    const LINK_PREVIOUS = 'previous';
    const LINK_NEXT = 'next';
    const LINK_METADATA = 'metadata';

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
            self::LINK_METADATA,
        ];
    }
}
