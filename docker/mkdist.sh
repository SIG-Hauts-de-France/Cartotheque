#!/bin/sh
### Script de construction des images distribuables.

cd "$(dirname "${0}")"

export UID_LOCAL=$(id -u)
export GID_LOCAL=$(id -g)

#docker-compose down

### Construction des images DEV
### (pour fonctionner avec code custom à monter en volumes)
docker-compose build

### Construction des images DIST
### (basées sur images DEV avec intégration du code custom)
docker-compose -f docker-compose.builder.yaml build

