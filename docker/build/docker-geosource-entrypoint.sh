#!/bin/sh
set -e

[ -z "${MAX_TIMEOUT}" ] && MAX_TIMEOUT=300  ## 5mn max
[ -z "${STP_TIMEOUT}" ] && STP_TIMEOUT=10   ## sleep 10s
[ -z "${DIR_GEOSOURCE}" ] && DIR_GEOSOURCE="/usr/local/tomcat/webapps/geosource"
[ ! -d "${DIR_GEOSOURCE}" ] && printf "\n*** Dossier webapp inexistant !\n\n" && exit 1

### /usr/local/tomcat/webapps/geosource/WEB-INF/config.xml
printf "\n=== Configuration : config.xml...\n"
[ -n "${DB_USER}" ] && sed -i "s|<user>[^<]*</user>|<user>${DB_USER}</user>|"                  "${DIR_GEOSOURCE}/WEB-INF/config.xml"
[ -n "${DB_PASS}" ] && sed -i "s|<password>[^<]*</password>|<password>${DB_PASS}</password>|"  "${DIR_GEOSOURCE}/WEB-INF/config.xml"
[ -n "${DB_HOST}" ] && sed -i "s|<url>jdbc:[^<]*</url>|<url>jdbc:postgis://${DB_HOST}:${DB_PORT:-5432}/${DB_BASE}</url>|"  "${DIR_GEOSOURCE}/WEB-INF/config.xml"
sed -n '/DEB DBConfig/,/FIN DBConfig/ p' "${DIR_GEOSOURCE}/WEB-INF/config.xml"

### /usr/local/tomcat/webapps/geosource/WEB-INF/config-node/srv.xml
printf "\n=== Configuration : config-node/srv.xml...\n"
cat "${DIR_GEOSOURCE}/WEB-INF/config-node/srv.xml"

### /usr/local/tomcat/webapps/geosource/WEB-INF/config-db/jdbc.properties
printf "\n=== Configuration : config-db/jdbc.properties...\n"
[ -n "${DB_HOST}" ] && sed -i "s/^\(jdbc\.host\)=.*\$/\1=${DB_HOST}/"      "${DIR_GEOSOURCE}/WEB-INF/config-db/jdbc.properties"
[ -n "${DB_BASE}" ] && sed -i "s/^\(jdbc\.database\)=.*\$/\1=${DB_BASE}/"  "${DIR_GEOSOURCE}/WEB-INF/config-db/jdbc.properties"
[ -n "${DB_USER}" ] && sed -i "s/^\(jdbc\.username\)=.*\$/\1=${DB_USER}/"  "${DIR_GEOSOURCE}/WEB-INF/config-db/jdbc.properties"
[ -n "${DB_PASS}" ] && sed -i "s/^\(jdbc\.password\)=.*\$/\1=${DB_PASS}/"  "${DIR_GEOSOURCE}/WEB-INF/config-db/jdbc.properties"
grep -v 'jdbc\.basic' "${DIR_GEOSOURCE}/WEB-INF/config-db/jdbc.properties"


### Boucle d'attente de disponibilité de la base de données
if [ -n "${DB_HOST}" ]; then
	printf "\n=== Attente de disponibilité de la base de données... (%ss max)\n" "${MAX_TIMEOUT}"
	while true; do
		pg_isready --host="${DB_HOST}" --port="${DB_PORT:-5432}" --timeout=0 --quiet && break
		[ "${MAX_TIMEOUT}" -le 0 ] && printf "\n*** Base de données indisponible !\n\n" && exit 2
		printf "... sleep waiting for db (${STP_TIMEOUT}/${MAX_TIMEOUT})\n"
		sleep "${STP_TIMEOUT}"
		MAX_TIMEOUT=`expr ${MAX_TIMEOUT} - ${STP_TIMEOUT}`
	done
fi

### Démarrage du serveur Tomcat
printf "\n=== Initialisation OK\n\n"
exec "$@"
