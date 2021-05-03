#!/bin/sh
set -e

[ -z "${MAX_TIMEOUT}" ] && MAX_TIMEOUT=1800  ## 30mn max
[ -z "${STP_TIMEOUT}" ] && STP_TIMEOUT=30    ## sleep 30s
SQL_DBLIST='\l'  ## PostgreSQL
DIR_DRUPAL="/var/www/html"


### Définition du compte utilisé pour le serveur web
[ -n "${USR_WORKER}" -a "${USR_WORKER}" -ne 0 ] || USR_WORKER='www-data'
[ -n "${GRP_WORKER}" -a "${GRP_WORKER}" -ne 0 ] || GRP_WORKER='www-data'
export APACHE_RUN_USER="$(id -n -u ${USR_WORKER})"
export APACHE_RUN_GROUP="$(id -n -g ${GRP_WORKER})"


### Réglage des permissions sur les fichiers et dossiers
fixPermissions() {
	FP_PATH="${1}"; FP_OGRP="${2}"; FP_DMOD="${3}"; FP_FMOD="${4}"
	[ -n "${FP_PATH}" -a -d "${FP_PATH}" ] || return 1
	printf "\n=== Initialisation du dossier '%s'...\n" "${FP_PATH}"
	[ -n "${FP_OGRP}" ] && chgrp -R "${FP_OGRP}" "${FP_PATH}"
	[ -n "${FP_DMOD}" ] && find "${FP_PATH}" -type d -print0 | xargs -0 --no-run-if-empty chmod "${FP_DMOD}"
	[ -n "${FP_FMOD}" ] && find "${FP_PATH}" -type f -print0 | xargs -0 --no-run-if-empty chmod "${FP_FMOD}"
}

fixPermissions "/var/www/drush-backups"            "${GRP_WORKER}" 'g+rwxs' 'g+rw'
fixPermissions "/var/www/private"                  "${GRP_WORKER}" 'g+rwxs' 'g+rw'
fixPermissions "${DIR_DRUPAL}/sites/default/files" "${GRP_WORKER}" '0775'   '0664'
fixPermissions "${DIR_DRUPAL}/sites/all"           "${GRP_WORKER}" '0775'   '0664'
chmod 0440 "${DIR_DRUPAL}/sites/default/"settings*.php || echo "*** WARNING: chown failed!"  # non-critical
chmod 2555 "${DIR_DRUPAL}/sites/default"


### Fin du script d'init si la commande drush n'est pas disponible
BIN_DRUSH="$(which drush 2>/dev/null || true)"
[ -z "${BIN_DRUSH}" ] && printf "\n*** Commande Drush non disponible (aucune configuration Drupal effectuée) !\n\n" && exit 1

### Afficher l'état de l'instance Drupal
cd "${DIR_DRUPAL}"
"${BIN_DRUSH}" core-status

### Boucle d'attente de disponibilité de la base de données
if [ -n "${DB_BASE}" ]; then
	printf "\n=== Attente de disponibilité de la base de données... (%ss max)\n" "${MAX_TIMEOUT}"
	while true; do
		echo "${SQL_DBLIST}" | "${BIN_DRUSH}" --yes sql-cli 2>/dev/null | grep "${DB_BASE}" -q && break
		[ "${MAX_TIMEOUT}" -le 0 ] && printf "\n*** Base de données indisponible !\n\n" && exit 2
		printf "... sleep waiting for db (${STP_TIMEOUT}/${MAX_TIMEOUT})\n"
		sleep "${STP_TIMEOUT}"
		MAX_TIMEOUT=`expr ${MAX_TIMEOUT} - ${STP_TIMEOUT}`
	done
fi


### Script d'installation du site de Drupal
DBSTATUS="$("${BIN_DRUSH}" --yes core-status --fields=db-status)"
printf "\n=== Drupal status : '${DBSTATUS}'\n"

if [ -z "${DBSTATUS}" ]; then
	
	## initialisation de la base de données
	printf "\n=== Drupal site : installation...\n"
	"${BIN_DRUSH}" site-install standard --yes -v \
		--account-pass="${ADMIN_PASS}" \
		--site-name="Cartothèque" --site-mail="${ADMIN_MAIL:-root@localhost}" \
		--locale='fr' install_configure_form.site_default_country='FR'
	
	
	## activation des extensions contrib
	printf "\n=== Postgres extensions : activation...\n"
	echo 'CREATE EXTENSION IF NOT EXISTS unaccent;' | "${BIN_DRUSH}" --yes sql-cli 2>/dev/null
	printf "\n=== Drupal extensions : activation...\n"
	"${BIN_DRUSH}" en -y \
	  admin_menu admin_views module_filter ctools devel smtp xautoload libraries \
	  views views_bulk_operations better_exposed_filters pgsql_combine_filter_views \
	  configuration clone bundle_copy override_node_options \
	  ldap_authentication ldap_authorization ldap_authorization_drupal_role ldap_servers ldap_user \
	  rules rules_admin term_merge tagadelic tagadelic_taxonomy taxonomy_csv \
	  facetapi search_api search_api_db search_api_views search_api_facetapi \
	  email url entity entity_token references node_reference autocomplete_deluxe \
	  date date_api date_popup date_views views_between_dates_filter \
	  jquery_update phpexcel redmine_rest_api image_url_formatter lightbox2 \
	  admin_menu_toolbar views_ui
	"${BIN_DRUSH}" dis -y toolbar
	
	## configuration initiale du module SMTP de Drupal
	printf "\n=== Drupal configuration : Module SMTP...\n"
	"${BIN_DRUSH}" vset -y --exact smtp_on               1
	"${BIN_DRUSH}" vset -y --exact smtp_deliver          1
	"${BIN_DRUSH}" vset -y --exact smtp_queue            0
	"${BIN_DRUSH}" vset -y --exact smtp_queue_fail       0
	"${BIN_DRUSH}" vset -y --exact smtp_protocol         "standard"
	"${BIN_DRUSH}" vset -y --exact smtp_hostbackup       --format=json '""'
	"${BIN_DRUSH}" vset -y --exact smtp_from             --format=json '""'
	"${BIN_DRUSH}" vset -y --exact smtp_fromname         --format=json '""'
	"${BIN_DRUSH}" vset -y --exact smtp_allowhtml        0
	"${BIN_DRUSH}" vset -y --exact smtp_client_hostname  --format=json '""'
	"${BIN_DRUSH}" vset -y --exact smtp_client_helo      --format=json '""'
	"${BIN_DRUSH}" vset -y --exact smtp_reroute_address  --format=json '""'
	"${BIN_DRUSH}" vset -y --exact smtp_debugging        2
	"${BIN_DRUSH}" vset -y --exact mail_system           --format=json '{"default-system": "SmtpMailSystem"}'
	"${BIN_DRUSH}" vset -y --exact smtp_previous_mail_system "DefaultMailSystem"
	
	## configuration de l'API Date
	printf "\n=== Drupal configuration : Date API...\n"
	"${BIN_DRUSH}" vset -y --exact date_api_use_iso8601  0
	"${BIN_DRUSH}" vset -y --exact date_first_day        1
	"${BIN_DRUSH}" vset -y --exact date_format_long      "l, j. F Y - G:i"
	"${BIN_DRUSH}" vset -y --exact date_format_medium    "D, d/m/Y - H:i"
	"${BIN_DRUSH}" vset -y --exact date_format_short     "d/m/Y - H:i"
	"${BIN_DRUSH}" vset -y --exact date_format_date_seulement "\\l\\e d/m/Y"
#	"${BIN_DRUSH}" vset -y --exact date_format_search_api_facetapi_YEAR    "Y"
#	"${BIN_DRUSH}" vset -y --exact date_format_search_api_facetapi_MONTH   "F Y"
#	"${BIN_DRUSH}" vset -y --exact date_format_search_api_facetapi_DAY     "F j, Y"
#	"${BIN_DRUSH}" vset -y --exact date_format_search_api_facetapi_HOUR    "H:__"
#	"${BIN_DRUSH}" vset -y --exact date_format_search_api_facetapi_MINUTE  "H:i"
#	"${BIN_DRUSH}" vset -y --exact date_format_search_api_facetapi_SECOND  "H:i:S"
	
	## configuration du dossier de stockage privé
	printf "\n=== Drupal configuration : Storage...\n"
	"${BIN_DRUSH}" vset -y --exact file_public_path      "sites/default/files"
	"${BIN_DRUSH}" vset -y --exact file_private_path     "../private"
	"${BIN_DRUSH}" vset -y --exact file_default_scheme   "private"
	
	
	## activation des extensions custom
	"${BIN_DRUSH}" en -y \
	  cartotheque tic_hdf tic_theme_hdf_update tic_geosource \
	  tic_carto_count tic_customsearch tic_redmine_data_importer tic_filedownload
	
	## import des termes des vocabulaires de taxonomie
	for SRC_TAXO in "${DIR_DRUPAL}/sites/default/fixtures"/taxonomy-*.csv; do
		REF_TAXO="$(echo "${SRC_TAXO}" | sed 's|^.*/taxonomy-\(.*\)\.csv$|\1|')"
		printf "\n=== Drupal import : vocabulaire '${REF_TAXO}'...\n"
		"${BIN_DRUSH}" taxocsv-import -y --keep_order --vocabulary_target=existing --vocabulary_id="${REF_TAXO}" "${SRC_TAXO}" fields
	done
	
	## configuration du thème personnalisé
	printf "\n=== Drupal configuration : Theme Cartotheque...\n"
	"${BIN_DRUSH}" vset -y --exact theme_default         "cartotheque"
	"${BIN_DRUSH}" vset -y --exact theme_cartotheque_settings --format=json '{
        "cartotheque_map_list_url": "?q=map-list-new",
        "default_favicon":      1,
        "default_logo":         1,
        "favicon_path":         "",
        "favicon_upload":       "",
        "logo_path":            "",
        "logo_upload":          "",
        "toggle_favicon":       1,
        "toggle_logo":          1,
        "toggle_name":          1,
        "toggle_slogan":        1,
        "jquery_update_jquery_version": "2.2"
    }'
	
	## configuration des modules de la cartothèque
	printf "\n=== Drupal configuration : Module Geosource...\n"
#	"${BIN_DRUSH}" vset -y --exact geosource_public_group_id          1
	"${BIN_DRUSH}" vset -y --exact geosource_public_group_id          2
	"${BIN_DRUSH}" vset -y --exact geosource_private_group_id         2
	"${BIN_DRUSH}" vset -y --exact geosource_server_timeout           180
	"${BIN_DRUSH}" vset -y --exact geosource_sync_delay               3600
	"${BIN_DRUSH}" vset -y --exact geosource_drupal_to_geosource      1
	"${BIN_DRUSH}" vset -y --exact geosource_geosource_to_drupal      0
	printf "\n=== Drupal configuration : Module CartoCount...\n"
	"${BIN_DRUSH}" vset -y --exact tic_carto_count_view_page_title    "Carto Download Counts"
	"${BIN_DRUSH}" vset -y --exact tic_carto_count_view_page_items    25
	"${BIN_DRUSH}" vset -y --exact tic_carto_count_view_page_limit    0
	"${BIN_DRUSH}" vset -y --exact tic_carto_count_flood_limit        0
	"${BIN_DRUSH}" vset -y --exact tic_carto_count_flood_window       5
	"${BIN_DRUSH}" vset -y --exact tic_carto_count_view_page_header --format=json '{"format": "filtered_html", "value": ""}'
	"${BIN_DRUSH}" vset -y --exact tic_carto_count_view_page_footer --format=json '{"format": "filtered_html", "value": ""}'
	
	
	## configuration des commentaires
	printf "\n=== Drupal configuration : Commentaires...\n"
	"${BIN_DRUSH}" vset -y --exact comment_default_mode_carte         1
	"${BIN_DRUSH}" vset -y --exact comment_default_mode_contact       1
	"${BIN_DRUSH}" vset -y --exact comment_default_mode_page          1
	"${BIN_DRUSH}" vset -y --exact comment_default_per_page_carte     50
	"${BIN_DRUSH}" vset -y --exact comment_default_per_page_contact   50
	"${BIN_DRUSH}" vset -y --exact comment_default_per_page_page      50
	"${BIN_DRUSH}" vset -y --exact comment_anonymous_carte            0
	"${BIN_DRUSH}" vset -y --exact comment_anonymous_contact          0
	"${BIN_DRUSH}" vset -y --exact comment_anonymous_page             0
	"${BIN_DRUSH}" vset -y --exact comment_carte                      0
	"${BIN_DRUSH}" vset -y --exact comment_contact                    0
	"${BIN_DRUSH}" vset -y --exact comment_form_location_carte        1
	"${BIN_DRUSH}" vset -y --exact comment_form_location_contact      1
	"${BIN_DRUSH}" vset -y --exact comment_form_location_page         1
	"${BIN_DRUSH}" vset -y --exact comment_page                       1
	"${BIN_DRUSH}" vset -y --exact comment_preview_carte              1
	"${BIN_DRUSH}" vset -y --exact comment_preview_contact            1
	"${BIN_DRUSH}" vset -y --exact comment_preview_page               1
	"${BIN_DRUSH}" vset -y --exact comment_subject_field_carte        1
	"${BIN_DRUSH}" vset -y --exact comment_subject_field_contact      1
	"${BIN_DRUSH}" vset -y --exact comment_subject_field_page         1
	
	## configuration des messages
	printf "\n=== Drupal configuration : Messages...\n"
	"${BIN_DRUSH}" vset -y --exact maintenance_mode_message                     "Cartothèque est en cours de maintenance. Nous serons de retour très bientôt. Merci de votre patience."
	"${BIN_DRUSH}" vset -y --exact user_mail_cancel_confirm_body                "[user:name],\r\n\r\nUne demande d'annulation de votre compte a été faite sur [site:name].\r\n\r\nVous pouvez maintenant annuler votre compte sur [site:url-brief] en cliquant sur ce lien ou en le copiant dans votre navigateur :\r\n\r\n[user:cancel-url]\r\n\r\nREMARQUE : L'annulation de votre compte n'est pas reversible.\r\n\r\nCe lien expirera après un jour et rien ne se passera s'il n'est pas utilisé.\r\n\r\n--  L'équipe [site:name]"
	"${BIN_DRUSH}" vset -y --exact user_mail_cancel_confirm_subject             "Demande d'annulation de compte pour [user:name] sur [site:name]"
	"${BIN_DRUSH}" vset -y --exact user_mail_password_reset_body                "[user:name],\r\n\r\nUne demande de renouvellement de mot de passe a été faite pour votre compte sur [site:name].\r\n\r\nVous pouvez désormais vous identifier en cliquant sur ce lien ou en le copiant dans votre navigateur :\r\n\r\n[user:one-time-login-url]\r\n\r\nCe lien ne peut être utilisé pour s'identifier qu'une seule fois et il vous conduira à une page où vous pourrez paramétrer votre mot de passe. Il expirera après un jour et rien ne se passera s'il n'est pas utilisé.\r\n\r\n--  L'équipe [site:name]"
	"${BIN_DRUSH}" vset -y --exact user_mail_password_reset_subject             "Informations de remplacement de connexion pour [user:name] sur [site:name]"
	"${BIN_DRUSH}" vset -y --exact user_mail_register_admin_created_body        "[user:name],\r\n\r\nUn administrateur du site [site:name] vous a créé un compte. Vous pouvez désormais vous identifier en cliquant sur ce lien ou en le copiant dans votre navigateur :\r\n\r\n[user:one-time-login-url]\r\n\r\nCe lien ne peut être utilisé pour s'identifier qu'une seule fois et il vous conduira à une page où vous pourrez paramétrer votre mot de passe.\r\n\r\nAprès avoir paramétré votre mot de passe, vous pourrez vous identifier à l'adresse [site:login-url] lors de vos prochaines connexions :\r\n\r\nidentifiant : [user:name]\r\nmot de passe : Votre mot de passe\r\n\r\n--  L'équipe [site:name]"
	"${BIN_DRUSH}" vset -y --exact user_mail_register_admin_created_subject     "Un administrateur a créé un compte pour vous sur [site:name]"
	"${BIN_DRUSH}" vset -y --exact user_mail_register_no_approval_required_body "[user:name],\r\n\r\nMerci de vous être enregistré sur [site:name]. Vous pouvez désormais vous identifier en cliquant sur ce lien ou en le copiant dans votre navigateur :\r\n\r\n[user:one-time-login-url]\r\n\r\nCe lien ne peut être utilisé pour s'identifier qu'une seule fois et il vous conduira à une page où vous pourrez paramétrer votre mot de passe.\r\n\r\nAprès avoir paramétré votre mot de passe, vous pourrez vous identifier à l'adresse [site:login-url] lors de vos prochaines connexions :\r\n\r\nidentifiant : [user:name]\r\nmot de passe : Votre mot de passe\r\n\r\n--  L'équipe [site:name]"
	"${BIN_DRUSH}" vset -y --exact user_mail_register_no_approval_required_subject "Détails du compte [user:name] sur [site:name]"
	"${BIN_DRUSH}" vset -y --exact user_mail_register_pending_approval_body     "[user:name],\r\n\r\nMerci pour votre inscription sur [site:name]. Votre demande de compte est actuellement en attente d'approbation. Quand elle aura été acceptée, vous recevrez un autre courriel contenant les informations pour vous connecter, définir votre mot de passe et d'autres détails.\r\n\r\n\r\n--  L'équipe [site:name]"
	"${BIN_DRUSH}" vset -y --exact user_mail_register_pending_approval_subject  "Détails du compte [user:name] sur [site:name] (en attente d'approbation)"
	"${BIN_DRUSH}" vset -y --exact user_mail_status_activated_body              "[user:name],\r\n\r\nVotre compte sur [site:name] a été activé.\r\n\r\nVous pouvez désormais vous identifier en cliquant sur ce lien ou en le copiant dans votre navigateur :\r\n\r\n[user:one-time-login-url]\r\n\r\nCe lien ne peut être utilisé pour s'identifier qu'une seule fois et il vous conduira à une page où vous pourrez paramétrer votre mot de passe.\r\n\r\nAprès avoir paramétré votre mot de passe, vous pourrez vous identifier à l'adresse [site:login-url] lors de vos prochaines connexions :\r\n\r\nidentifiant : [user:name]\r\nmot de passe : Votre mot de passe\r\n\r\n--  L'équipe [site:name]"
	"${BIN_DRUSH}" vset -y --exact user_mail_status_activated_notify            1
	"${BIN_DRUSH}" vset -y --exact user_mail_status_activated_subject           "Détails du compte [user:name] sur [site:name] (approuvé)"
	"${BIN_DRUSH}" vset -y --exact user_mail_status_blocked_body                "[user:name],\r\n\r\nVotre compte sur [site:name] a été bloqué.\r\n\r\n--  L'équipe de [site:name]"
	"${BIN_DRUSH}" vset -y --exact user_mail_status_blocked_notify              0
	"${BIN_DRUSH}" vset -y --exact user_mail_status_blocked_subject             "Détails du compte [user:name] sur [site:name] (bloqué)"
	"${BIN_DRUSH}" vset -y --exact user_mail_status_canceled_body               "[user:name],\r\n\r\nVotre compte sur [site:name] a été annulé.\r\n\r\n--  L'équipe [site:name]"
	"${BIN_DRUSH}" vset -y --exact user_mail_status_canceled_notify             0
	"${BIN_DRUSH}" vset -y --exact user_mail_status_canceled_subject            "Détails du compte [user:name] sur [site:name] (annulé)"
	
	
	"${BIN_DRUSH}" php-eval 'node_access_rebuild();'
fi


### Reparamétrage du module SMTP de Drupal
if [ -n "${SMTP_HOST}" ]; then
	printf "\n=== Drupal paramétrage : SMTP...\n"
	"${BIN_DRUSH}" vset -y --exact smtp_host            --format=json "\"${SMTP_HOST}\""
	"${BIN_DRUSH}" vset -y --exact smtp_port            --format=json "\"${SMTP_PORT}\""
	"${BIN_DRUSH}" vset -y --exact smtp_username        --format=json "\"${SMTP_USER}\""
	"${BIN_DRUSH}" vset -y --exact smtp_password        --format=json "\"${SMTP_PASS}\""
fi

### Reparamétrage de l'interconnexion Geosource
if [ -n "${GEO_HOST}" ]; then
	printf "\n=== Drupal paramétrage : Geosource...\n"
	"${BIN_DRUSH}" vset -y --exact geosource_server_url               "${GEO_HOST}/srv/fre/csw-publication"
	"${BIN_DRUSH}" vset -y --exact geosource_server_auth_address      "${GEO_HOST}"
	"${BIN_DRUSH}" vset -y --exact geosource_server_user              "${GEO_USER:-admin}"
	"${BIN_DRUSH}" vset -y --exact geosource_server_password          "${GEO_PASS:-p4ssw0rd}"
fi

### Reparamétrage de l'interconnexion Redmine
if [ -n "${RED_URL}" ]; then
	printf "\n=== Drupal paramétrage : Redmine...\n"
	"${BIN_DRUSH}" vset -y --exact redmine_rest_api_redmine_version   "${RED_VER:-2.5}"
	"${BIN_DRUSH}" vset -y --exact redmine_rest_api_redmine_base_url  "${RED_URL}"
	"${BIN_DRUSH}" vset -y --exact redmine_rest_api_api_key           "${RED_KEY}"
	"${BIN_DRUSH}" vset -y --exact cors_domains --format=json         "{\"*\": \"${RED_URL}\"}"
fi

### Mise à jour des thèmes HdF des cartes
if [ -f "${DIR_DRUPAL}/sites/default/fixtures/Cartes_production_200228_V2.xlsx" ]; then
	printf "\n=== Drupal import : màj thèmes HdF...\n"
	"${BIN_DRUSH}" import-update-theme-hdf -y "${DIR_DRUPAL}/sites/default/fixtures/Cartes_production_200228_V2.xlsx"
fi


### Finalisation des paramétrages
printf "\n=== Drupal database upgrades...\n"
cd "${DIR_DRUPAL}" && "${BIN_DRUSH}" updatedb -y
printf "\n=== Drupal cache reinitialisation...\n"
cd "${DIR_DRUPAL}" && "${BIN_DRUSH}" cache-clear -y all

### Démarrage du serveur Apache/PHP
printf "\n=== Initialisation OK\n\n"
exec /usr/local/bin/docker-php-entrypoint "$@"
