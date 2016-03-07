<?php

namespace RayRutjes\GetEventStore;

final class EventRecord
{
    /**
     * @var string
     */
    private $streamId;

    /**
     * @var int
     */
    private $number;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $metadata;

    /**
     * @param string $streamId
     * @param int    $number
     * @param string $type
     * @param array  $data
     * @param array  $metadata
     */
    public function __construct(string $streamId, int $number, string $type, array $data, array $metadata)
    {
        $this->streamId = $streamId;
        $this->number = $number;
        $this->type = $type;
        $this->data = $data;
        $this->metadata = $metadata;
    }

    /**
     * @return string
     */
    public function getStreamId(): string
    {
        return $this->streamId;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }
}
