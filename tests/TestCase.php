<?php

namespace RayRutjes\GetEventStore\Test;

use Ramsey\Uuid\Uuid;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return string
     */
    protected function newUuid()
    {
        return Uuid::uuid4()->toString();
    }
}
