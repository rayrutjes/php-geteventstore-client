<?php

namespace RayRutjes\GetEventStore;

final class ReadDirection
{
    const FORWARD = 1;
    const BACKWARD = 0;

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        if ($value !== self::FORWARD && $value !== self::BACKWARD) {
            throw new \InvalidArgumentException('Invalid direction.');
        }

        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function isForward(): bool
    {
        return $this->value === self::FORWARD;
    }

    /**
     * @return bool
     */
    public function isBackward(): bool
    {
        return $this->value === self::BACKWARD;
    }
}
