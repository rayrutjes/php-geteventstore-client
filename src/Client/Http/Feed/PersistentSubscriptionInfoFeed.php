<?php

namespace RayRutjes\GetEventStore\Client\Http\Feed;

use RayRutjes\GetEventStore\PersistentSubscriptionInfo;

final class PersistentSubscriptionInfoFeed
{
    /**
     * @var array
     */
    private $links = [];

    /**
     * @var PersistentSubscriptionInfo
     */
    private $info;

    /**
     * @param array                      $links
     * @param PersistentSubscriptionInfo $info
     */
    public function __construct(array $links, PersistentSubscriptionInfo $info)
    {
        foreach ($links as $link) {
            $this->validateLink($link);
            $this->links[$link->getRelation()] = $link;
        }
        $this->info = $info;
    }

    /**
     * @param $link
     */
    private function validateLink($link)
    {
        if (!$link instanceof PersistentSubscriptionInfoFeedLink) {
            throw new \InvalidArgumentException('Invalid link type %s.', get_class($link));
        }
        if (isset($this->links[$link->getRelation()])) {
            throw new \InvalidArgumentException(sprintf('Link relation %s already there.', $link->getRelation()));
        }
    }

    /**
     * @return PersistentSubscriptionInfo
     */
    public function getPersistentSubscriptionInfo(): PersistentSubscriptionInfo
    {
        return $this->info;
    }
}
