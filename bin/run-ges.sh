#!/usr/bin/env bash

docker run --name geteventstore --detach --publish 2113:2113 --publish 1113:1113 adbrowne/eventstore