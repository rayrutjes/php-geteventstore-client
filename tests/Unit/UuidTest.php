<?php

namespace RayRutjes\GetEventStore\Test\Unit;

use RayRutjes\GetEventStore\Test\TestCase;
use RayRutjes\GetEventStore\Uuid;

class UuidTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldNotAcceptInvalidUuids()
    {
        new Uuid('invalid-uuid');
    }

    public function testCanBeReturnedAsString()
    {
        $uuid = $this->newUuid();
        $cut = new Uuid($uuid);
        $this->assertEquals($uuid, $cut->toString());
    }
}
