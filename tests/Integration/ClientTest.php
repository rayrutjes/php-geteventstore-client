<?php

namespace RayRutjes\GetEventStore\Test\Integration;

use RayRutjes\GetEventStore\ExpectedVersion;

class ClientTest extends IntegrationTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAppendToStreamExpectsAtLeastOneEvent()
    {
        $client = $this->buildClient();
        $client->appendToStream('stream', ExpectedVersion::ANY, []);
    }

    public function testCanAppendSingleEvent()
    {
        $events = $this->getEventDataSet(1);
        $streamId = uniqid('testCanAppendSingleEvent');
        $client = $this->buildClient();
        $client->appendToStream($streamId, ExpectedVersion::ANY, $events);

        $records = $client->readAllEventsFromStream($streamId);
        $this->assertEventDataMatchesEventRecords($events, $records, $streamId);
    }

    public function testCanDeleteAStream()
    {
        $events = $this->getEventDataSet(2);
        $client = $this->buildClient();

        // We generate a unique stream to be deleted because once gone, the stream name
        // can not be re-used.
        $streamId = uniqid('testCanDeleteAStream');
        $client->appendToStream($streamId, ExpectedVersion::ANY, $events);
        $client->deleteStream($streamId);
    }

    /**
     * @expectedException \RayRutjes\GetEventStore\Client\Exception\StreamDeletedException
     */
    public function testDeleteStreamShouldBeGone()
    {
        $events = $this->getEventDataSet(2);
        $client = $this->buildClient();

        // We generate a unique stream to be deleted because once gone, the stream name
        // can not be re-used.
        $streamId = uniqid('testDeleteStreamShouldBeGone');
        $client->appendToStream($streamId, ExpectedVersion::ANY, $events);
        $client->deleteStream($streamId);

        // We can not append to a deleted stream.
        $client->appendToStream($streamId, ExpectedVersion::ANY, $events);
    }

    public function testCanAppendMultipleEvents()
    {
        $events = $this->getEventDataSet(3);
        $streamId = uniqid('testCanAppendMultipleEvents');
        $client = $this->buildClient();
        $client->appendToStream($streamId, ExpectedVersion::ANY, $events);

        $records = $client->readAllEventsFromStream($streamId);
        $this->assertEventDataMatchesEventRecords($events, $records, $streamId);
    }

    public function testCanReadAllEventsOfAStream()
    {
        $events = $this->getEventDataSet(3);
        $streamId = uniqid('testCanReadAllEventsOfAStream');
        $client = $this->buildClient();
        $client->appendToStream($streamId, ExpectedVersion::ANY, $events);

        $records = $client->readAllEventsFromStream($streamId);
        $this->assertEventDataMatchesEventRecords($events, $records, $streamId);
    }

    public function testCanReadStreamUpToVersion()
    {
        $events = $this->getEventDataSet(10);
        $streamId = uniqid('testCanReadStreamUpToVersion');
        $client = $this->buildClient();
        $client->appendToStream($streamId, ExpectedVersion::ANY, $events);

        // We missed a single event.
        $records = $client->readStreamUpToVersion($streamId, 9 - 1);
        $this->assertEventDataMatchesEventRecords(array_slice($events, 9 + 1 - 1), $records, $streamId, 9 + 1 - 1);

        // We missed 5 events.
        $records = $client->readStreamUpToVersion($streamId, 9 - 5);
        $this->assertEventDataMatchesEventRecords(array_slice($events, 9 + 1 - 5), $records, $streamId, 9 + 1 - 5);

        // We are up to date.
        $records = $client->readStreamUpToVersion($streamId, 9 - 0);
        $this->assertEventDataMatchesEventRecords(array_slice($events, 9 + 1 - 0), $records, $streamId, 9 + 1 - 0);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testStreamCatchUpShouldValidateVersion()
    {
        $streamId = uniqid('testCanReadStreamUpToVersion');
        $client = $this->buildClient();
        $client->readStreamUpToVersion($streamId, -1);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanNotCatchUpAnOutOfBoundVersion()
    {
        $events = $this->getEventDataSet(3);
        $streamId = uniqid('testCanNotCatchUpAnOutOfBoundVersion');
        $client = $this->buildClient();
        $client->appendToStream($streamId, ExpectedVersion::ANY, $events);

        $client->readStreamUpToVersion($streamId, 5);
    }

    public function testCanReadAllEvents()
    {
        $client = $this->buildClient();
        $client->readAllEvents();
        // todo: this is pretty hard to test.
        // todo: I think this function should somehow filter the system and metadata events.
    }
}
