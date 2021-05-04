### Script à lancer lors de l'initialisation de la base de données (MariaDB ou PostgreSQL).
### NOTE: script non exécutable pour être sourcé dans le script docker-entrypoint.sh (et profiter de son "$@")

[ -z "${IMPORT_FOLDER}" ] && IMPORT_FOLDER="/var/dump/initdb"

[ -d "${IMPORT_FOLDER}" ] && docker_process_init_files "${IMPORT_FOLDER}"/*

