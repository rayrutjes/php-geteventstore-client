<?php

namespace RayRutjes\GetEventStore;

class ExpectedVersion
{
    /**
     * Ensures that the stream exists but contains no events.
     */
    const EMPTY_STREAM = 0;

    /**
     * Ensures that the stream does not exist.
     */
    const NO_STREAM = -1;

    /**
     * Will always succeed. This should not be used if you to keep your event store consistent.
     */
    const ANY = -2;

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        if ($value < self::ANY) {
            throw new \InvalidArgumentException(sprintf('Wrong expected version value, got: %d', $value));
        }
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function toInt(): int
    {
        return $this->value;
    }
}
