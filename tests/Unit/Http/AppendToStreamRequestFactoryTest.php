<?php

namespace RayRutjes\GetEventStore\Unit\Http;

use RayRutjes\GetEventStore\Client\Http\AppendToStreamRequestFactory;
use RayRutjes\GetEventStore\Client\Http\ContentType;
use RayRutjes\GetEventStore\Client\Http\RequestHeader;
use RayRutjes\GetEventStore\EventData;
use RayRutjes\GetEventStore\EventDataCollection;
use RayRutjes\GetEventStore\ExpectedVersion;
use RayRutjes\GetEventStore\StreamId;
use RayRutjes\GetEventStore\Test\TestCase;

class AppendToStreamRequestFactoryTest extends TestCase
{
    public function testCanBuildARequest()
    {
        $uuid1 = $this->newUuid();
        $type1 = 'RayRutjes\GetEventStore\FakeEvent1';
        $data1 = ['a' => 'test1'];
        $metadata1 = [];
        $event1 = new EventData($uuid1, $type1, $data1, $metadata1);

        $uuid2 = $this->newUuid();
        $type2 = 'RayRutjes\GetEventStore\FakeEvent2';
        $data2 = ['a' => 'test2'];
        $metadata2 = ['test' => 'value'];
        $event2 = new EventData($uuid2, $type2, $data2, $metadata2);

        $expectedBody = <<<'EOD'
[
    {
        "eventId": "%s",
        "eventType": "RayRutjes\\GetEventStore\\FakeEvent1",
        "data": {"a":"test1"},
        "metadata":[]
    },
    {
        "eventId": "%s",
        "eventType": "RayRutjes\\GetEventStore\\FakeEvent2",
        "data": {"a":"test2"},
        "metadata": {"test":"value"}
    }
]
EOD;
        $expectedBody = sprintf(str_replace([' ', "\n"], '', $expectedBody), $uuid1, $uuid2);

        $cut = new AppendToStreamRequestFactory(
            new StreamId('stream'),
            new ExpectedVersion(ExpectedVersion::ANY),
            EventDataCollection::fromArray([$event1, $event2])
        );
        $request = $cut->buildRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('streams/stream', $request->getUri()->getPath());
        $this->assertEquals(ContentType::JSON_ES, $request->getHeaderLine(RequestHeader::CONTENT_TYPE));
        $this->assertEmpty($request->getHeader(RequestHeader::EVENT_ID));
        $this->assertEmpty($request->getHeader(RequestHeader::EVENT_TYPE));
        $this->assertEquals(ExpectedVersion::ANY, $request->getHeaderLine(RequestHeader::EXPECTED_VERSION));
        $this->assertEquals($expectedBody, $request->getBody()->getContents());
    }
}
