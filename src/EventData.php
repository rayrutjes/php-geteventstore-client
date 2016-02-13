<?php

namespace RayRutjes\GetEventStore;

final class EventData
{
    /**
     * @var Uuid
     */
    private $eventId;

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
     * @param string $eventId
     * @param string $type
     * @param array  $data
     * @param array  $metadata
     */
    public function __construct(string $eventId, string $type, array $data, array $metadata)
    {
        $this->eventId = new Uuid($eventId);
        $this->type = $type;
        $this->data = $data;
        $this->metadata = $metadata;
    }

    /**
     * @return Uuid
     */
    public function getEventId(): Uuid
    {
        return $this->eventId;
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
