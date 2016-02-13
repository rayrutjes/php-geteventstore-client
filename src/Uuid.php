<?php

namespace RayRutjes\GetEventStore;

use Ramsey\Uuid\UuidInterface;

class Uuid
{
    /**
     * @var UuidInterface
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (!\Ramsey\Uuid\Uuid::isValid($value)) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid Uuid.', $value));
        }
        $this->value = \Ramsey\Uuid\Uuid::fromString($value);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->value->toString();
    }
}
