<?php

namespace RayRutjes\GetEventStore;

final class PersistentSubscriptionSettings
{
    const STRATEGY_ROUND_ROBIN = 'RoundRobin';
    const STRATEGY_DISPATCH_TO_SINGLE = 'DispatchToSingle';
    const CURRENT_POSITION = -1;

    /**
     * @var bool
     */
    private $resolveLinktos = true;

    /**
     * @var string
     */
    private $namedConsumerStrategy = self::STRATEGY_ROUND_ROBIN;

    /**
     * @var int
     */
    private $startFrom = self::CURRENT_POSITION;

    /**
     * @var int
     */
    private $messageTimeoutMilliseconds = 3;

    /**
     * @var int
     */
    private $checkPointAfterMilliseconds = 3;

    /**
     * @var int
     */
    private $minCheckPointCount = 100;

    /**
     * @var int
     */
    private $maxCheckPointCount = 500;

    /**
     * @var int
     */
    private $maxRetryCount = 3;

    /**
     * @var bool
     */
    private $extraStatistics = false;

    /**
     * @var int
     */
    private $readBatchSize = 20;

    /**
     * @var int
     */
    private $maxSubscriberCount = 10;

    /**
     * Tells the subscription to resolve link events.
     *
     * @return PersistentSubscriptionSettings
     */
    public function resolveLinktos(): PersistentSubscriptionSettings
    {
        $this->resolveLinktos = true;

        return $this;
    }

    /**
     * Tells the subscription to not resolve link events.
     *
     * @return PersistentSubscriptionSettings
     */
    public function doNotResolveLinktos(): PersistentSubscriptionSettings
    {
        $this->resolveLinktos = false;

        return $this;
    }

    /**
     * If possible prefer to round robin between the connections with messages (if not possible will use next available).
     *
     * @return PersistentSubscriptionSettings
     */
    public function preferRoundRobin(): PersistentSubscriptionSettings
    {
        $this->namedConsumerStrategy = self::STRATEGY_ROUND_ROBIN;

        return $this;
    }

    /**
     * If possible prefer to dispatch to a single connection (if not possible will use next available).
     *
     * @return PersistentSubscriptionSettings
     */
    public function preferDispatchToSingle(): PersistentSubscriptionSettings
    {
        $this->namedConsumerStrategy = self::STRATEGY_DISPATCH_TO_SINGLE;

        return $this;
    }

    /**
     * Start the subscription from the position-th event in the stream.
     *
     * @return PersistentSubscriptionSettings
     */
    public function startFromBeginning(): PersistentSubscriptionSettings
    {
        $this->startFrom = 0;

        return $this;
    }

    /**
     * Start the subscription from the position-th event in the stream.
     *
     * @param int $position
     *
     * @return PersistentSubscriptionSettings
     */
    public function startFrom(int $position): PersistentSubscriptionSettings
    {
        if ($position < 0) {
            throw new \InvalidArgumentException(sprintf('Position must be > 0, Got: %d', $position));
        }
        $this->startFrom = $position;

        return $this;
    }

    /**
     * Start the subscription from the current position.
     *
     * @return PersistentSubscriptionSettings
     */
    public function startFromCurrent(): PersistentSubscriptionSettings
    {
        $this->startFrom = self::CURRENT_POSITION;

        return $this;
    }

    /**
     * Sets the timeout in milliseconds for a client before the message will be retried.
     *
     * @param int $timeout
     *
     * @return PersistentSubscriptionSettings
     */
    public function withMessageTimeoutInMillisecondsOf(int $timeout): PersistentSubscriptionSettings
    {
        if ($timeout <= 0) {
            throw new \InvalidArgumentException(sprintf('Timeout must be > 0, Got: %d', $timeout));
        }

        $this->messageTimeoutMilliseconds = $timeout;

        return $this;
    }

    /**
     * The amount of time the system should try to checkpoint after.
     *
     * @param int $time
     *
     * @return PersistentSubscriptionSettings
     */
    public function checkPointAfter(int $time): PersistentSubscriptionSettings
    {
        if ($time <= 0) {
            throw new \InvalidArgumentException(sprintf('Time must be > 0, Got: %d', $time));
        }

        $this->checkPointAfterMilliseconds = $time;

        return $this;
    }

    /**
     * The minimum number of messages to write a checkpoint for.
     *
     * @param int $count
     *
     * @return PersistentSubscriptionSettings
     */
    public function minCheckPointOf(int $count): PersistentSubscriptionSettings
    {
        if ($count <= 0) {
            throw new \InvalidArgumentException(sprintf('Count must be > 0, Got: %d', $count));
        }

        $this->minCheckPointCount = $count;

        return $this;
    }

    /**
     * The maximum number of messages not checkpointed before forcing a checkpoint.
     *
     * @param int $count
     *
     * @return PersistentSubscriptionSettings
     */
    public function maxCheckPointOf(int $count): PersistentSubscriptionSettings
    {
        if ($count <= 0) {
            throw new \InvalidArgumentException(sprintf('Count must be > 0, Got: %d', $count));
        }

        if ($count < $this->minCheckPointCount) {
            throw new \InvalidArgumentException(sprintf('Maximum checkpoint count %d should be > to min checkpoint count %d.', $count, $this->minCheckPointCount));
        }

        $this->maxCheckPointCount = $count;

        return $this;
    }

    /**
     * Sets the number of times a message should be retried before being considered a bad message.
     *
     * @param int $count
     *
     * @return PersistentSubscriptionSettings
     */
    public function withMaxRetriesOf(int $count): PersistentSubscriptionSettings
    {
        if ($count < 0) {
            throw new \InvalidArgumentException(sprintf('Max retries count must be >= 0, Got: %d', $count));
        }

        $this->maxRetryCount = $count;

        return $this;
    }

    /**
     * 	The size of the read batch when in paging mode.
     *
     * @param int $count
     *
     * @return PersistentSubscriptionSettings
     */
    public function WithReadBatchOf(int $count): PersistentSubscriptionSettings
    {
        if ($count < 0) {
            throw new \InvalidArgumentException(sprintf('Read batch must be > 0, Got: %d', $count));
        }

        $this->readBatchSize = $count;

        return $this;
    }

    /**
     * Tells the backend to measure timings on the clients so statistics will contain histograms of them.
     *
     * @return PersistentSubscriptionSettings
     */
    public function withExtraStatistics(): PersistentSubscriptionSettings
    {
        $this->extraStatistics = true;

        return $this;
    }

    /**
     * @param int $count
     */
    public function withMaxSubscribersOf(int $count)
    {
        if ($count <= 0) {
            throw new \InvalidArgumentException(sprintf('Max subscribers count must be > 0, Got: %d', $count));
        }

        $this->maxSubscriberCount = $count;

        return $this;
    }

    /**
     * Sets the maximum number of allowed subscribers.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'checkPointAfterMilliseconds' => $this->checkPointAfterMilliseconds,
            'extraStatistics'             => $this->extraStatistics,
            'maxCheckPointCount'          => $this->maxCheckPointCount,
            'minCheckPointCount'          => $this->minCheckPointCount,
            'maxRetryCount'               => $this->maxRetryCount,
            'namedConsumerStrategy'       => $this->namedConsumerStrategy,
            'readBatchSize'               => $this->readBatchSize,
            'messageTimeoutMilliseconds'  => $this->messageTimeoutMilliseconds,
            'namedConsumerStrategy'       => $this->namedConsumerStrategy,
            'maxSubscriberCount'          => $this->maxSubscriberCount,
            'resolveLinktos'              => $this->resolveLinktos,
        ];
    }
}
