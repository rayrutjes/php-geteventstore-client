<?php

namespace RayRutjes\GetEventStore\Test\Integration;

use RayRutjes\GetEventStore\EventRecord;
use RayRutjes\GetEventStore\EventRecordCollection;
use RayRutjes\GetEventStore\ExpectedVersion;
use RayRutjes\GetEventStore\PersistentSubscriptionSettings;

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

    // Disabled for now.
//    public function testCanReadAllEvents()
//    {
//        $client = $this->buildClient();
//        $client->readAllEvents();
//        // todo: this is pretty hard to test.
//        // todo: I think this function should somehow filter the system and metadata events.
//    }

    public function testCanCreatePersistentSubscription()
    {
        $settings = new PersistentSubscriptionSettings();
        $client = $this->buildClient();
        $client->createPersistentSubscription('stream', 'group', $settings);

        $expected = $settings->toArray();
        $result = $client->getPersistentSubscriptionInfo('stream', 'group');
        $this->assertEquals('stream', $result->getStreamId()->toString());
        $this->assertEquals('group', $result->getGroupName());
        $this->assertEquals($expected, $result->getSettings()->toArray());
    }

    public function testCanUpdatePersistentSubscription()
    {
        $settings = new PersistentSubscriptionSettings();
        $settings->doNotResolveLinktos()
            ->checkPointAfter(5000)
            ->maxCheckPointOf(666)
            ->minCheckPointOf(444)
            ->preferDispatchToSingle()
            ->startFrom(3)
            ->withExtraStatistics()
            ->withMaxRetriesOf(66)
            // todo: waiting for a fix to add this test.
            // ->withMaxSubscribersOf(99)
            ->withMessageTimeoutInMillisecondsOf(666)
            ->WithReadBatchOf(66);

        $client = $this->buildClient();
        $client->updatePersistentSubscription('stream', 'group', $settings);

        $expected = $settings->toArray();
        $result = $client->getPersistentSubscriptionInfo('stream', 'group');
        $this->assertEquals('stream', $result->getStreamId()->toString());
        $this->assertEquals('group', $result->getGroupName());
        $this->assertEquals($expected, $result->getSettings()->toArray());
    }

    public function testCanDeletePersistentSubscription()
    {
        $client = $this->buildClient();
        $client->deletePersistentSubscription('stream', 'group');
    }

    /**
     * @dataProvider differentCounts
     */
    public function testCanReadEventsThroughPersistentSubscriptions($counts)
    {
        $client = $this->buildClient();

        $expectedCount = $counts;

        // Create a new stream of events.
        $streamId = uniqid('testCanReadEventsThroughPersistentSubscriptions');

        // Create a persistent subscription.
        $settings = new PersistentSubscriptionSettings();
        $client->createPersistentSubscription($streamId, 'group', $settings);

        $events = $this->getEventDataSet($expectedCount);
        if ($expectedCount > 0) {
            $client->appendToStream($streamId, ExpectedVersion::ANY, $events);
        }

        $records = [];
        $client->readStreamViaPersistentSubscription($streamId, 'group', function (EventRecord $event) use (&$records) {
            $records[] = $event;
        }, 20);

        $eventsCollection = EventRecordCollection::fromArray($records);

        // Ensure all events are returned.
        $this->assertEventDataMatchesEventRecords($events, $eventsCollection, $streamId, 0);
    }

    public function differentCounts()
    {
        return [
            [0],
            [3],
            [20],
            [30],
            [100],
        ];
    }
}
