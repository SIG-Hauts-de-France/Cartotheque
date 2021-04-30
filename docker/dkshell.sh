#!/bin/sh

cd "$(dirname "${0}")"

if [ $# -gt 0 ]
	then docker-compose exec drupal "$@"
	else docker-compose exec drupal bash
fi

