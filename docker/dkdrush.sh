#!/bin/sh

cd "$(dirname "${0}")"

#docker-compose exec drupal /opt/composer/vendor/bin/drush "$@"
docker-compose exec drupal drush "$@"

