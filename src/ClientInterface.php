<?php

namespace RayRutjes\GetEventStore;

interface ClientInterface
{
    /**
     * @param string $streamId
     * @param int    $expectedVersion
     * @param array  $events
     */
    public function appendToStream(string $streamId, int $expectedVersion, array $events);

    /**
     * @param string $streamId
     */
    public function deleteStream(string $streamId);

    /**
     * With great power comes great responsibility.
     *
     * @return EventRecordCollection
     */
    public function readAllEvents() : EventRecordCollection;

    /**
     * Retrieves events recorded since a given version of the stream.
     * Does not include the event with number corresponding to the given version.
     *
     * @param string $streamId
     * @param int    $version
     *
     * @return EventRecordCollection
     */
    public function readStreamUpToVersion(string $streamId, int $version) : EventRecordCollection;

    /**
     * @param string $streamId
     *
     * @return EventRecordCollection
     */
    public function readAllEventsFromStream(string $streamId) : EventRecordCollection;

    /**
     * @param string                         $streamId
     * @param string                         $groupName
     * @param PersistentSubscriptionSettings $settings
     */
    public function createPersistentSubscription(string $streamId, string $groupName, PersistentSubscriptionSettings $settings);

    /**
     * @param string $streamId
     * @param string $groupName
     */
    public function updatePersistentSubscription(string $streamId, string $groupName);

    /**
     * @param string $streamId
     * @param string $groupName
     */
    public function deletePersistentSubscription(string $streamId, string $groupName);

    /**
     * @param string   $streamId
     * @param string   $groupName
     * @param callable $messageHandler
     * @param int      $bufferSize
     * @param bool     $autoAck
     */
    public function readStreamViaPersistentSubscription(string $streamId, string $groupName, callable $messageHandler, int $bufferSize = 1, bool $autoAck = true);
}
