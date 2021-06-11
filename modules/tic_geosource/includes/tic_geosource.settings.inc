<?php

function tic_geosource_config($form, &$form_state) {
	
	$form['geosource server'] = array(
		'#type' => 'fieldset',
		'#title' => 'Configuration serveur GeoNetwork',
		'#collapsible' => FALSE,
	);
	
	$form['geosource server']['geosource_server_url'] = array(
		'#type' => 'textfield',
		'#title' => 'Adresse serveur CSW GeoNetwork',
		'#default_value' => variable_get('geosource_server_url', 'http://geosourcedemo.brgm.fr/geosource/srv/eng/csw'),
		'#maxlength' => 255,
		'#description' => 'Adresse du serveur GeoNetwork a utiliser pour la synchronisation des cartes',
	);
	$form['geosource server']['geosource_server_user'] = array(
		'#type' => 'textfield',
		'#title' => 'Nom d\'utilisateur GeoNetwork',
		'#default_value' => variable_get('geosource_server_user', ''),
		'#maxlength' => 255,
		'#description' => 'Identifiant GeoNetwork pour la synchronisation de cartes',
	);
	$form['geosource server']['geosource_server_password'] = array(
		'#type' => 'textfield',
		'#title' => 'Mot de passe GeoNetwork',
		'#default_value' => variable_get('geosource_server_password', ''),
		'#maxlength' => 255,
		'#description' => 'Mot de passe du serveur GeoNetwork pour la synchronisation de cartes',
	);
	$form['geosource server']['geosource_server_auth_address'] = array(
		'#type' => 'textfield',
		'#title' => 'Adresse GeoNetwork',
		'#default_value' => variable_get('geosource_server_auth_address', ''),
		'#maxlength' => 255,
		'#description' => 'Adresse de l\'authentification GeoNetwork pour la synchronisation de cartes',
	);
	$form['geosource server']['geosource_server_timeout'] = array(
		'#type' => 'textfield',
		'#title' => 'Timeout',
		'#default_value' => variable_get('geosource_server_timeout', 60),
		'#maxlength' => 3,
		'#description' => 'Délai de timeout pour la connexion GeoNetwork',
	);
	$form['geosource server']['geosource_public_group_id'] = array(
		'#type' => 'textfield',
		'#title' => 'Id groupe publique',
		'#default_value' => variable_get('geosource_public_group_id', ''),
		'#description' => 'Identifiant du groupe publique dans GeoNetwork',
	);
	$form['geosource server']['geosource_private_group_id'] = array(
		'#type' => 'textfield',
		'#title' => 'Id groupe privé',
		'#default_value' => variable_get('geosource_private_group_id', ''),
		'#description' => 'Identifiant du groupe privé dans GeoNetwork',
	);
	$form['geosource sync'] = array(
		'#type' => 'fieldset',
		'#title' => 'Options de synchronisation GeoNetwork',
		'#collapsible' => false,
	);
	$form['geosource sync']['geosource_sync_delay'] = array(
		'#type' => 'textfield',
		'#title' => 'Intervalle de synchronisation',
		'#default_value' => variable_get('geosource_sync_delay', ''),
		'#description' => 'Délai entre deux synchronisations avec GeoNetwork (en secondes)',
	);
	$form['geosource_sync']['geosource_drupal_to_geosource'] = array(
		'#type' => 'checkbox',
		'#title' => 'Synchronisation Drupal => GeoNetwork',
		'#default_value' => variable_get('geosource_drupal_to_geosource', 0),
	);
	$form['geosource_sync']['geosource_geosource_to_drupal'] = array(
		'#type' => 'checkbox',
		'#title' => 'Synchronisation GeoNetwork => Drupal',
		'#default_value' => variable_get('geosource_geosource_to_drupal', 0),
	);
	// TODO: utilisateur pour les cartes créées par la synchro
	// TODO: auteur par défaut pour les cartes créées par la synchro n'ayant pas d'email associé
	
	return system_settings_form($form);
}

function tic_geosource_sync() {
	$pageContent = array();
	$error = false;
	$queuedItems = 0;
	
	module_load_include('inc', 'tic_geosource', 'includes/tic_geosource.utils');

	$geosourceToDrupal = variable_get('geosource_geosource_to_drupal', 0);

	if ($geosourceToDrupal != 0) {
	
		try {
			$client = tic_geosource_load_csw_client();
			//TODO: ajouter une méthode getAll dans cswGeoClient.php
			//$xml = $client->getRecordsModifiedSince(new DateTime('01-01-2000'));
			//$allUuids = tic_geosource_get_result_uuids($xml);
			$uuids = $client->jsonGetRecordsModifiedSince('', 'map');
			
			// Ajouter les uuids a traiter dans le queue
			$queue = DrupalQueue::get('tic_geosource_sync');
			foreach ($uuids as $uuid) {
				$queue->createItem($uuid);
				$queuedItems++;
			}
			
			$uuids = $client->jsonGetRecordsModifiedSince('', 'service');
			if (is_array($uuids)) {
				foreach ($uuids as $uuid) {
					$queue->createItem($uuid);
					$queuedItems++;
				}
			}
		}
		catch (Exception $e) {
			watchdog('tic_geosource', 'Erreur a la synchronisation initiale: '.$e->getMessage());
			$error = true;
		}

		if ($error == false) {
			$pageContent['#markup'] = '<p>La synchronisation avec GeoNetwork a été lancée ('.$queuedItems.' fiches ajoutées dans la file d\'attente)</p>';
		}
		else {
			$pageContent['#markup'] = '<p>Une erreur est survenue lors du lancement de la synchronisation avec GeoNetwork</p>';
		}
	}
	else {
		$pageContent['#markup'] = '<p>La synchronisation de GeoNetwork vers Drupal doit être activée pour utiliser cette fonctionnalité</p>';
	}
	
	drupal_set_title('Synchronisation avec GeoNetwork');
	
	return drupal_render($pageContent);
}

function tic_geosource_configtest() {
	$pageContent = array();
	$error = false;
	$statusStr = 'OK';
	$errorStr = '';
	
	$server = variable_get('geosource_server_url', '');
	$username = variable_get('geosource_server_user', '');
	$password = variable_get('geosource_server_password', '');
	$auth = variable_get('geosource_server_auth_address', '');
	
	//Charger client csw
	try {
		$client = tic_geosource_load_csw_client();
		//$records = $client->getCountRecords();
		$records = $client->getCapabilities();
		
		$dom = new DOMDocument('1.0');
		if ($dom->loadXML((string)$records)) {
			
			$capabilities = '<br />';
			
			$caps = $dom->getElementsByTagName('OperationsMetadata');
			foreach($caps->item(0)->getElementsByTagName('Operation') as $cap) {
				$capabilities .= '- ' . $cap->getAttribute('name') . '<br />';
			}
		}
		else {
			$error = true;
			$statusStr = 'ERREUR';
			$errorStr = 'Impossible de parser le XML: '.check_markup($records);
		}
	} catch (Exception $e) {
		$error = true;
		$statusStr = 'ERREUR';
		$errorStr = $e->getMessage();
	}
	
	drupal_set_title('Test de la configuration GeoNetwork');
	$pageContent['#markup'] = '<h1>Paramètres de connexion</h1>';
	$pageContent['#markup'] .= '<table><tr><td>Serveur</td><td>'.$server.'</td></tr>';
	$pageContent['#markup'] .= '<tr><td>Utilisateur</td><td>'.$username.'</td></tr>';
	$pageContent['#markup'] .= '<tr><td>Mot de passe</td><td>'.$password.'</td></tr>';
	$pageContent['#markup'] .= '<tr><td>URL d\'authentification:</td><td>'.$auth.'</td></tr></table>';
	
	if($auth == '') {
		$pageContent['#markup'] .= '<b>Attention:</b> Aucune adresse d\'authentification n\'a été fournie. L\'authentification ne sera pas utilisée';
	}
	
	$pageContent['#markup'] .= '<h1>Résultat du test de connexion: '.$statusStr.'</h1>';
	
	if(!$error) {
		$pageContent['#markup'] .= '<p>Connexion à GeoNetwork réussie. Liste des capabilities du serveur: ' . $capabilities . '</p>';
	}
	else {
		$pageContent['#markup'] .= '<p>Echec de la connexion à GeoNetwork. Il semble que vos paramètres ne soient pas corrects...</p>';
		$pageContent['#markup'] .= '<h2>Informations complémentaires: </h2>'.$errorStr;
	}
	
	return drupal_render($pageContent);
}
