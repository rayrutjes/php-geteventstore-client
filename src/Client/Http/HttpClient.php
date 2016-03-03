<?php

namespace RayRutjes\GetEventStore\Client\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RayRutjes\GetEventStore\ClientInterface;
use RayRutjes\GetEventStore\Client\Exception\SystemException;
use RayRutjes\GetEventStore\Client\Http\Feed\EventStreamFeedIterator;
use RayRutjes\GetEventStore\Client\Http\Feed\EventStreamIterator;
use RayRutjes\GetEventStore\EventDataCollection;
use RayRutjes\GetEventStore\EventRecordCollection;
use RayRutjes\GetEventStore\ExpectedVersion;
use RayRutjes\GetEventStore\PersistentSubscriptionSettings;
use RayRutjes\GetEventStore\StreamId;
use RayRutjes\GetEventStore\UserCredentials;

final class HttpClient implements ClientInterface
{
    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @param string          $baseUri
     * @param UserCredentials $credentials
     * @param float           $connectTimeout
     * @param array           $httpClientOptions
     */
    public function __construct(
        string $baseUri,
        UserCredentials $credentials,
        float $connectTimeout = 3,
        array $httpClientOptions = []
    ) {
        $options = array_merge(
            $httpClientOptions,
            [
                'base_uri'        => $baseUri,
                'allow_redirects' => false,
                'connect_timeout' => $connectTimeout,
                'auth'            => [$credentials->getLogin(), $credentials->getPassword()],
                'http_errors'     => false, // Let the client handle the status codes for now.
            ]
        );
        $this->httpClient = new Client($options);
    }

    /**
     * @param string $streamId
     * @param int    $expectedVersion
     * @param array  $events
     */
    public function appendToStream(string $streamId, int $expectedVersion, array $events)
    {
        $events = EventDataCollection::fromArray($events);
        if (0 === $events->count()) {
            throw new \InvalidArgumentException('No events provided.');
        }

        $streamId = new StreamId($streamId);
        if ($streamId->isSystem()) {
            throw new \InvalidArgumentException(sprintf('Can not append to system stream %s', $streamId));
        }

        $expectedVersion = new ExpectedVersion($expectedVersion);

        $request = new AppendToStreamRequestFactory($streamId, $expectedVersion, $events);

        $this->send($request->buildRequest(), new AppendToStreamResponseInspector());
    }

    /**
     * @param RequestInterface  $request
     * @param ResponseInspector $inspector
     *
     * @return ResponseInterface
     *
     * @internal
     */
    public function send(RequestInterface $request, ResponseInspector $inspector): ResponseInterface
    {
        try {
            $response = $this->httpClient->send($request);
        } catch (TransferException $e) {
            $this->handleTransferException($e);
        }

        $inspector->inspect($response);

        return $response;
    }

    /**
     * @param $e
     */
    private function handleTransferException(TransferException $e)
    {
        throw new SystemException($e->getMessage(), $e->getCode(), $e);
    }

    /**
     * @param string $streamId
     */
    public function deleteStream(string $streamId)
    {
        $streamId = new StreamId($streamId);
        if ($streamId->isSystem()) {
            throw new \InvalidArgumentException(
                sprintf('Can not delete system stream with id %s', $streamId->toString())
            );
        }

        $factory = new DeleteStreamRequestFactory($streamId);
        $this->send($factory->buildRequest(), new DeleteStreamResponseInspector());
    }

    /**
     * With great power comes great responsibility.
     *
     * @return EventRecordCollection
     */
    public function readAllEvents(): EventRecordCollection
    {
        $streamId = new StreamId(StreamId::ALL);

        return $this->readAllEventsFromStream($streamId->toString());
    }

    /**
     * @param string $streamId
     *
     * @return EventRecordCollection
     */
    public function readAllEventsFromStream(string $streamId): EventRecordCollection
    {
        $streamId = new StreamId($streamId);
        $feedsIterator = new EventStreamFeedIterator($streamId, $this, true);
        $eventsIterator = new EventStreamIterator($feedsIterator);

        $events = [];
        foreach ($eventsIterator as $event) {
            $events[] = $event;
        }

        return EventRecordCollection::fromArray($events);
    }

    /**
     * Retrieves events recorded since a given version of the stream.
     * Does not include the event with number corresponding to the given version.
     *
     * @param string $streamId
     * @param int    $version
     *
     * @return EventRecordCollection
     */
    public function readStreamUpToVersion(string $streamId, int $version): EventRecordCollection
    {
        if ($version <= 0) {
            throw new \InvalidArgumentException(sprintf('version should be >= 0, got: %d', $version));
        }

        $streamId = new StreamId($streamId);
        // Todo: there are probably more streams to avoid. Thinking of system or metadata streams.
        if ($streamId->toString() === StreamId::ALL) {
            throw new \InvalidArgumentException(sprintf('Can not catch up %s stream.', StreamId::ALL));
        }

        $feedsIterator = new EventStreamFeedIterator($streamId, $this, false);
        $eventsIterator = new EventStreamIterator($feedsIterator);

        $events = [];
        foreach ($eventsIterator as $event) {
            if ($event->getNumber() < $version) {
                throw new \InvalidArgumentException(
                    sprintf('Stream %s has not reached version %d.', $streamId->toString(), $version)
                );
            }

            if ($event->getNumber() === $version) {
                break;
            }
            $events[] = $event;
        }

        $events = array_reverse($events);

        return EventRecordCollection::fromArray($events);
    }

    /**
     * @param string                         $streamId
     * @param string                         $groupName
     * @param PersistentSubscriptionSettings $settings
     */
    public function createPersistentSubscription(
        string $streamId,
        string $groupName,
        PersistentSubscriptionSettings $settings
    ) {
        $streamId = new StreamId($streamId);
        $factory = new CreatePersistentSubscriptionRequestFactory($streamId, $groupName, $settings);
        $this->send($factory->buildRequest(), new CreatePersistentSubscriptionResponseInspector());
    }

    /**
     * @param string                         $streamId
     * @param string                         $groupName
     * @param PersistentSubscriptionSettings $settings
     */
    public function updatePersistentSubscription(string $streamId, string $groupName, PersistentSubscriptionSettings $settings)
    {
        $streamId = new StreamId($streamId);
        $factory = new UpdatePersistentSubscriptionRequestFactory($streamId, $groupName, $settings);
        $this->send($factory->buildRequest(), new UpdatePersistentSubscriptionResponseInspector());
    }

    /**
     * @param string $streamId
     * @param string $groupName
     */
    public function deletePersistentSubscription(string $streamId, string $groupName)
    {
        $streamId = new StreamId($streamId);
        $factory = new DeletePersistentSubscriptionRequestFactory($streamId, $groupName);
        $this->send($factory->buildRequest(), new DeletePersistentSubscriptionResponseInspector());
    }

    /**
     * @param string   $streamId
     * @param string   $groupName
     * @param callable $messageHandler
     * @param int      $bufferSize
     * @param bool     $autoAck
     */
    public function readStreamViaPersistentSubscription(
        string $streamId,
        string $groupName,
        callable $messageHandler,
        int $bufferSize = 1,
        bool $autoAck = true
    ) {
        // TODO: Implement readStreamViaPersistentSubscription() method.
    }
}
