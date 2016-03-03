<?php

namespace RayRutjes\GetEventStore\Client\Http;

class ContentType
{
    const JSON = 'application/json';
    const JSON_ES = 'application/vnd.eventstore.events+json';
    const ATOM_JSON = 'application/vnd.eventstore.atom+json';
    const COMPETING_ATOM_JSON = 'application/vnd.eventstore.competingatom+json';
}
