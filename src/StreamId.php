<?php

namespace RayRutjes\GetEventStore;

final class StreamId
{
    const ALL = '$all';

    /**
     * @var string
     */
    private $value;

    /**
     * @var bool
     */
    private $isSystem = false;

    /**
     * @var bool
     */
    private $isMetadata = false;

    /**
     * EventStream constructor.
     *
     * @param string $value
     */
    public function __construct(string $value = '')
    {
        // todo: add some additional alphanum + $ check.

        if (empty($value)) {
            $value = self::ALL;
        }

        if ($this->startsWith($value, '$$')) {
            $this->isMetadata = true;
        } elseif ($this->startsWith($value, '$')) {
            $this->isSystem = true;
        }
        $this->value = $value;
    }

    /**
     * @param $value
     * @param $prefix
     *
     * @return bool
     */
    private function startsWith(string $value, string $prefix): bool
    {
        return strrpos($value, $prefix, -strlen($value)) !== false;
    }

    /**
     * @return bool
     */
    public function isSystem(): bool
    {
        return $this->isSystem;
    }

    /**
     * @return bool
     */
    public function isMetadata(): bool
    {
        return $this->isMetadata;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }
}
