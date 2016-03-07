<?php

namespace RayRutjes\GetEventStore\Client\Http\Feed;

abstract class AbstractFeedLink
{
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
    final public function __construct(string $uri, string $relation)
    {
        $validRelations = $this->getValidRelations();
        if (!in_array($relation, $validRelations)) {
            throw new \InvalidArgumentException(sprintf('Invalid link relation %s.', $relation));
        }
        $this->uri = $uri;
        $this->relation = $relation;
    }

    /**
     * @return array
     */
    abstract protected function getValidRelations(): array;

    /**
     * @return string
     */
    final public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    final public function getRelation(): string
    {
        return $this->relation;
    }
}
