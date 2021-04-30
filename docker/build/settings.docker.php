<?php
/**
 * Configuration de l'instance Drupal : paramètres spécifiques de l'instance
 */

### Docker database configuration
if (! isset($databases['default'])) $databases['default'] = array();
if (getenv('DB_BASE')) $databases['default']['default'] = array (
	'driver'    => 'pgsql',
	'host'      => getenv('DB_HOST'),
	'port'      => getenv('DB_PORT'),
	'database'  => getenv('DB_BASE'),
	'username'  => getenv('DB_USER'),
	'password'  => getenv('DB_PASS'),
	'prefix'    => '',
);

### Dynamic site URL
if (! isset($base_url)) {
	$cur_host = isset($_SERVER["SERVER_NAME"])    ? $_SERVER["SERVER_NAME"]    : (isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : 'localhost');
	$cur_prot = isset($_SERVER["REQUEST_SCHEME"]) ? $_SERVER["REQUEST_SCHEME"] : 'http';
	$cur_port = isset($_SERVER["SERVER_PORT"])    ? $_SERVER["SERVER_PORT"]    : getenv('PORT_WEB');
	$def_port = ($cur_prot === 'https') ? 443 : 80;
	$base_url = $cur_prot . "://" . $cur_host;
	if ($cur_port && $cur_port != $def_port) $base_url.= ":" . $cur_port;
}

### Debug mode PHP/Drupal ?
if (getenv('PHP_DEBUG')) {
	error_reporting(E_ALL);
	ini_set('display_errors',         TRUE);
	ini_set('display_startup_errors', TRUE);
	$conf['error_level'] = intval(getenv('PHP_DEBUG'));
	$conf['theme_debug'] = TRUE;
}

if (is_dir("/var/www/drush-backups")) $options['backup-dir'] = "/var/www/drush-backups";

### Drupal salt generated from Docker
if (is_file("/var/www/drupal-salt.txt")) $drupal_hash_salt = file_get_contents("/var/www/drupal-salt.txt");

### Access control for update.php script
$update_free_access = FALSE;

$conf['drupal_http_request_fails'] = FALSE;


