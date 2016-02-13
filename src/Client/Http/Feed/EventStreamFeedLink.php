<?php

namespace RayRutjes\GetEventStore\Client\Http\Feed;

class EventStreamFeedLink
{
    const LINK_SELF = 'self';
    const LINK_FIRST = 'first';
    const LINK_LAST = 'last';
    const LINK_PREVIOUS = 'previous';
    const LINK_NEXT = 'next';
    const LINK_METADATA = 'metadata';

    /**
     * @var array
     */
    private $validRelations = [self::LINK_SELF, self::LINK_FIRST, self::LINK_LAST, self::LINK_PREVIOUS, self::LINK_NEXT, self::LINK_METADATA];

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $relation;

    /**
     * @param string $uri
     * @param string $relation
     */
    public function __construct(string $uri, string $relation)
    {
        if (!in_array($relation, $this->validRelations)) {
            throw new \InvalidArgumentException(sprintf('Invalid link relation %s.', $relation));
        }
        $this->uri = $uri;
        $this->relation = $relation;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getRelation(): string
    {
        return $this->relation;
    }
}
