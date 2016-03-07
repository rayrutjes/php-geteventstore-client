<?php

namespace RayRutjes\GetEventStore\Test\Integration;

use RayRutjes\GetEventStore\Client\Http\HttpClient;
use RayRutjes\GetEventStore\ClientInterface;
use RayRutjes\GetEventStore\EventData;
use RayRutjes\GetEventStore\EventRecordCollection;
use RayRutjes\GetEventStore\Test\TestCase;
use RayRutjes\GetEventStore\UserCredentials;

abstract class IntegrationTestCase extends TestCase
{
    protected $baseUri;

    public function setUp()
    {
        $this->baseUri = $_SERVER['GES_BASE_URI'] ?? 'http://127.0.0.1:2113';
    }

    /**
     * @param UserCredentials $credentials
     * @param float           $connectTimeout
     * @param array           $options
     *
     * @return ClientInterface
     */
    protected function buildClient(UserCredentials $credentials = null, float $connectTimeout = 0, array $options = [])
    {
        if (null === $credentials) {
            $credentials = $this->adminCredentials();
        }

        return new HttpClient($this->baseUri, $credentials, $connectTimeout, $options);
    }

    /**
     * @return UserCredentials
     */
    protected function adminCredentials()
    {
        return new UserCredentials('admin', 'changeit');
    }

    /**
     * @return EventData
     */
    protected function fakeEvent($index)
    {
        return new EventData(
            $this->newUuid(),
            'GetEventStore\\FakeEventType',
            ['a' => 'Test data' . $index],
            ['b' => 'Test metadata']
        );
    }

    /**
     * @param int $size
     *
     * @return array
     */
    protected function getEventDataSet($size = 3)
    {
        $events = [];
        for ($i = 1; $i <= $size; $i++) {
            $events[] = $this->fakeEvent($i);
        }

        return $events;
    }

    /**
     * @param array                 $expectedEvents
     * @param EventRecordCollection $records
     * @param string                $streamId
     * @param int                   $offset
     */
    protected function assertEventDataMatchesEventRecords(array $expectedEvents, EventRecordCollection $records, string $streamId, int $offset = 0)
    {
        /** @var EventRecord $record */
        foreach ($records as $key => $record) {
            /** @var EventData $data */
            $data = array_shift($expectedEvents);
            $this->assertEquals($data->getData(), $record->getData());
            $this->assertEquals($data->getType(), $record->getType());
            $this->assertEquals($key + $offset, $record->getNumber());
            $this->assertEquals($streamId, $record->getStreamId());
            $this->assertEquals($data->getMetadata(), $record->getMetadata());

            // todo: Add event id tests.
            // $this->assertEquals($data->getEventId(), $record->getEventId());
        }

        $this->assertEmpty($expectedEvents);
    }
}
