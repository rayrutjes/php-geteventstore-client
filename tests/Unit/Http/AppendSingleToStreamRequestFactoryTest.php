<?php

namespace RayRutjes\GetEventStore\Unit\Http;

use RayRutjes\GetEventStore\Client\Http\AppendSingleToStreamRequestFactory;
use RayRutjes\GetEventStore\Client\Http\ContentType;
use RayRutjes\GetEventStore\Client\Http\RequestHeader;
use RayRutjes\GetEventStore\EventData;
use RayRutjes\GetEventStore\ExpectedVersion;
use RayRutjes\GetEventStore\StreamId;
use RayRutjes\GetEventStore\Test\TestCase;

class AppendSingleToStreamRequestFactoryTest extends TestCase
{
    public function testCanBuildARequest()
    {
        $uuid = $this->newUuid();
        $type = 'RayRutjes\\GetEventStore\\FakeEvent';
        $data = ['a' => 'test'];
        $metadata = [];
        $event = new EventData($uuid, $type, $data, $metadata);

        $cut = new AppendSingleToStreamRequestFactory(new StreamId('stream'), new ExpectedVersion(ExpectedVersion::ANY), $event);
        $request = $cut->buildRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('streams/stream', $request->getUri()->getPath());
        $this->assertEquals(ContentType::JSON, $request->getHeaderLine(RequestHeader::CONTENT_TYPE));
        $this->assertEquals($uuid, $request->getHeaderLine(RequestHeader::EVENT_ID));
        $this->assertEquals($type, $request->getHeaderLine(RequestHeader::EVENT_TYPE));
        $this->assertEquals(ExpectedVersion::ANY, $request->getHeaderLine(RequestHeader::EXPECTED_VERSION));
        $this->assertEquals('{"a":"test"}', $request->getBody()->getContents());
    }
}
