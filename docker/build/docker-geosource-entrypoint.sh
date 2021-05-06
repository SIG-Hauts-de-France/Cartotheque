#!/bin/sh
set -e

################################################################################ VARS

[ -z "${MAX_TIMEOUT}"   ] && MAX_TIMEOUT=300
[ -z "${STP_TIMEOUT}"   ] && STP_TIMEOUT=10
[ -z "${CATALINA_HOME}" ] && CATALINA_HOME="/usr/local/tomcat"
[ -z "${DIR_GEOSOURCE}" ] && DIR_GEOSOURCE="${CATALINA_HOME}/webapps/geosource"
[ ! -d "${DIR_GEOSOURCE}" ] && printf "\n*** Dossier webapp inexistant !\n\n" && exit 1

### Adresse IP de l'hôte & adresses IP du container
IP_HOST="$(ip route show default | awk '/^default/ {print $3}')"
IP_CONT="$(ip -brief -4 addr     | awk '{print $3}')"


################################################################################ CONF GEOSOURCE

### /usr/local/tomcat/webapps/geosource/WEB-INF/config.xml
printf "\n=== [geosource] Configuration : config.xml...\n"
[ -n "${DB_USER}" ] && sed -i "s|<user>[^<]*</user>|<user>${DB_USER}</user>|"                  "${DIR_GEOSOURCE}/WEB-INF/config.xml"
[ -n "${DB_PASS}" ] && sed -i "s|<password>[^<]*</password>|<password>${DB_PASS}</password>|"  "${DIR_GEOSOURCE}/WEB-INF/config.xml"
[ -n "${DB_HOST}" ] && sed -i "s|<url>jdbc:[^<]*</url>|<url>jdbc:postgis://${DB_HOST}:${DB_PORT:-5432}/${DB_BASE}</url>|"  "${DIR_GEOSOURCE}/WEB-INF/config.xml"
sed -n '/DEB DBConfig/,/FIN DBConfig/ p'                                                       "${DIR_GEOSOURCE}/WEB-INF/config.xml"

### /usr/local/tomcat/webapps/geosource/WEB-INF/config-node/srv.xml
printf "\n=== [geosource] Configuration : config-node/srv.xml...\n"
cat "${DIR_GEOSOURCE}/WEB-INF/config-node/srv.xml"

### /usr/local/tomcat/webapps/geosource/WEB-INF/config-db/jdbc.properties
printf "\n=== [geosource] Configuration : config-db/jdbc.properties...\n"
[ -n "${DB_HOST}" ] && sed -i "s/^\(jdbc\.host\)=.*\$/\1=${DB_HOST}/"      "${DIR_GEOSOURCE}/WEB-INF/config-db/jdbc.properties"
[ -n "${DB_BASE}" ] && sed -i "s/^\(jdbc\.database\)=.*\$/\1=${DB_BASE}/"  "${DIR_GEOSOURCE}/WEB-INF/config-db/jdbc.properties"
[ -n "${DB_USER}" ] && sed -i "s/^\(jdbc\.username\)=.*\$/\1=${DB_USER}/"  "${DIR_GEOSOURCE}/WEB-INF/config-db/jdbc.properties"
[ -n "${DB_PASS}" ] && sed -i "s/^\(jdbc\.password\)=.*\$/\1=${DB_PASS}/"  "${DIR_GEOSOURCE}/WEB-INF/config-db/jdbc.properties"
grep -v 'jdbc\.basic'                                                      "${DIR_GEOSOURCE}/WEB-INF/config-db/jdbc.properties"


################################################################################ CONF MANAGER

### /usr/local/tomcat/conf/tomcat-users.xml
printf "\n=== [manager] Configuration : conf/tomcat-users.xml...\n"
[ -z "${TC_PASS}" ] && TC_PASS="$(head -c12 /dev/urandom | base64 -w0)"
sed -i "/username=/ s|password=\"[^\"]*\"|password=\"${TC_PASS}\"|"        "${CATALINA_HOME}/conf/tomcat-users.xml"
grep '<user '                                                              "${CATALINA_HOME}/conf/tomcat-users.xml"

### /usr/local/tomcat/webapps/manager/META-INF/context.xml
printf "\n=== [manager] Configuration : META-INF/context.xml...\n"
[ -z "${TC_IPRE}" ] && TC_IPRE="$(echo "${IP_CONT}" | sed 's|\.[0-9]*/[0-9]*|.\\\\d+|' | sed 's/\./\\\\./g' | tr "\n" "|")"
sed -i "s/allow=\".*::1/allow=\"${TC_IPRE}::1/"                            "${CATALINA_HOME}/webapps/manager/META-INF/context.xml"
grep 'allow='                                                              "${CATALINA_HOME}/webapps/manager/META-INF/context.xml"


################################################################################ CONF JKSTATUS

### /etc/apache2/sites-available/001-geosource.conf
printf "\n=== [jkstatus] Configuration : 001-geosource.conf...\n"
[ -z "${JK_FROM}" ] && JK_FROM="${IP_HOST}"
sed -i "s|\(Require\s\+ip\s\+\).*\$|\1${JK_FROM}|"                         "/etc/apache2/sites-available/001-geosource.conf"
grep 'Require\s\+ip'                                                       "/etc/apache2/sites-available/001-geosource.conf"
#echo "${TC_PASS}" | htpasswd -ciB "/etc/apache2/admin-passwords" "jkadmin" ## utilisable pour auth httpd


################################################################################ INIT SERVICES

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

### Suppression de variables sensibles
unset -v DB_PASS TC_PASS

### Démarrage du serveur HTTPd
printf "\n=== Démarrage du service Apache HTTPd\n\n"
APACHE_ARGUMENTS=''
[ -z "${WITH_ADMIN_TC}" ] || APACHE_ARGUMENTS="-DtcManagerEnabled ${APACHE_ARGUMENTS}"
[ -z "${WITH_ADMIN_JK}" ] || APACHE_ARGUMENTS="-DjkManagerEnabled ${APACHE_ARGUMENTS}"
export APACHE_ARGUMENTS
#. "/etc/apache2/envvars"
#/usr/sbin/apache2 ${APACHE_ARGUMENTS} -k start
/usr/sbin/apache2ctl start

### Démarrage du serveur Tomcat
printf "\n=== Initialisation OK\n\n"
exec "$@"
