<?php

namespace RayRutjes\GetEventStore;

final class MessageAction
{
    /**
     * Adds the message to the park queue.
     */
    const PARK = 'Park';

    /**
     * Retry the message.
     */
    const RETRY = 'Retry';

    /**
     * Forget about the message.
     */
    const SKIP = 'Skip';

    /**
     * @var string
     */
    private $action;

    /**
     * @param string $action
     */
    public function __construct(string $action)
    {
        switch ($action) {
            case self::PARK:
            case self::RETRY:
            case self::SKIP:
                break;
            default:
                throw new \InvalidArgumentException(sprintf('%s is an invalid message action.', $action));
        }
        $this->action = $action;
    }
}
