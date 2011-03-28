#!/bin/sh

exec dvsink-command -- \
  dvpiperespawn `which dvsink-icecast2-enc.sh` \
| dvpiperespawn `which oggfwd` -p -n "LAC 2011 Live" \
	localhost 5900 hackme test.ogv 
