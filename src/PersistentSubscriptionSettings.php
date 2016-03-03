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
    private $resolveLinkTos = true;

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
    private $timeout = 3;

    /**
     * @var int
     */
    private $checkpointAfter = 3;

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
    private $maxRetries = 3;

    /**
     * @var int
     */
    private $bufferSize = 20;

    /**
     * @var int
     */
    private $liveBufferSize = 20;

    /**
     * @var bool
     */
    private $extraStatistics = false;

    /**
     * @var int
     */
    private $readBatch = 20;

    /**
     * Tells the subscription to resolve link events.
     *
     * @return PersistentSubscriptionSettings
     */
    public function resolveLinkTos(): PersistentSubscriptionSettings
    {
        $this->resolveLinkTos = true;

        return $this;
    }

    /**
     * Tells the subscription to not resolve link events.
     *
     * @return PersistentSubscriptionSettings
     */
    public function doNotResolveLinkTos(): PersistentSubscriptionSettings
    {
        $this->resolveLinkTos = false;

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
     * Sets the timeout for a client before the message will be retried.
     *
     * @param int $timeout
     *
     * @return PersistentSubscriptionSettings
     */
    public function withMessageTimeoutOf(int $timeout): PersistentSubscriptionSettings
    {
        if ($timeout <= 0) {
            throw new \InvalidArgumentException(sprintf('Timeout must be > 0, Got: %d', $timeout));
        }

        $this->timeout = $timeout;

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

        $this->checkpointAfter = $time;

        return $this;
    }

    /**
     * The minimum number of messages to write a checkpoint for.
     *
     * @param int $count
     *
     * @return PersistentSubscriptionSettings
     */
    public function minCheckPointCountOf(int $count): PersistentSubscriptionSettings
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
    public function maxCheckPointCountOf(int $count): PersistentSubscriptionSettings
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

        $this->maxRetries = $count;

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

        $this->readBatch = $count;

        return $this;
    }

    /**
     * The number of messages that should be buffered when in paging mode.
     *
     * @param int $count
     *
     * @return PersistentSubscriptionSettings
     */
    public function withBufferSizeOf(int $count): PersistentSubscriptionSettings
    {
        if ($count <= 0) {
            throw new \InvalidArgumentException(sprintf('Buffer size should be > 0, Got: %d', $count));
        }

        $this->bufferSize = $count;

        return $this;
    }

    /**
     * The size of the live buffer (in memory) before resorting to paging.
     *
     * @param int $count
     *
     * @return PersistentSubscriptionSettings
     */
    public function withLiveBufferSizeOf(int $count): PersistentSubscriptionSettings
    {
        if ($count <= 0) {
            throw new \InvalidArgumentException(sprintf('Live buffer size should be > 0, Got: %d', $count));
        }

        $this->liveBufferSize = $count;

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
}
