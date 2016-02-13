[![Build Status](https://travis-ci.org/rayrutjes/php-geteventstore-client.svg)](https://travis-ci.org/rayrutjes/php-geteventstore-client)
[![License](https://img.shields.io/github/license/rayrutjes/php-geteventstore-client.svg)](LICENSE.md)
[![Dependencies](https://img.shields.io/gemnasium/rayrutjes/php-geteventstore-client.svg)](https://gemnasium.com/rayrutjes/php-geteventstore-client)
[![Code Quality](https://img.shields.io/scrutinizer/g/rayrutjes/php-geteventstore-client.svg)](https://scrutinizer-ci.com/g/rayrutjes/php-geteventstore-client/)

# PHP GetEventStore client

This is a PHP client library for communicating with [GetEventStore](https://geteventstore.com/).

For now the it as been tested against V3.4.0 API, it has yet to be tested against other versions.

## Requirements

Your PHP version should be at least 7.0.0., I think this shouldn't be a problem. 

Event Sourcing generally has to be considered from the start of your project.

And as you are starting a new project, why not start it in PHP 7?

## Installation

```bash
$ composer require rayrutjes/php-geteventstore-client
```

For now there only is an http client. You need to append Guzzle to your project.
 
 ```bash
 $ composer require guzzlehttp/guzzle
 ```

## Testing

```bash
$ composer update
$ vendor/bin/phpunit
```

Tests will be looking for an environment variable `GES_BASE_URI`. If none is found, it will use the default `http://127.0.0.1:2113` uri for communication with the event store.

## Some opinionated choices

- We never use xml as a content type for http messages as it makes bodies grow in size.
- For now, we use embed bodies in feeds to lower the number of http requests.

## Contributing

Please feel free to contribute by any means.