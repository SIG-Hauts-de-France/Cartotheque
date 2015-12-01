<?php

function tic_geosource_config($form, &$form_state) {
	
	$form['geosource server'] = array(
		'#type' => 'fieldset',
		'#title' => 'Configuration serveur Geosource',
		'#collapsible' => FALSE,
	);
	
	$form['geosource server']['geosource_server_url'] = array(
		'#type' => 'textfield',
		'#title' => 'Adresse serveur Geosource',
		'#default_value' => variable_get('geosource_server_url', 'http://geosourcedemo.brgm.fr/geosource/srv/eng/csw'),
		'#maxlength' => 255,
		'#description' => 'Adresse du serveur Geosource a utiliser pour la synchronisation des cartes',
	);
	$form['geosource server']['geosource_server_user'] = array(
		'#type' => 'textfield',
		'#title' => 'Nom d\'utilisateur Geosource',
		'#default_value' => variable_get('geosource_server_user', ''),
		'#maxlength' => 255,
		'#description' => 'Identifiant Geosource pour la synchronisation de cartes',
	);
	$form['geosource server']['geosource_server_password'] = array(
		'#type' => 'textfield',
		'#title' => 'Mot de passe Geosource',
		'#default_value' => variable_get('geosource_server_password', ''),
		'#maxlength' => 255,
		'#description' => 'Mot de passe du serveur Geosource pour la synchronisation de cartes',
	);
	$form['geosource server']['geosource_server_auth_address'] = array(
		'#type' => 'textfield',
		'#title' => 'Adresse d\'authentification Geosource',
		'#default_value' => variable_get('geosource_server_auth_address', ''),
		'#maxlength' => 255,
		'#description' => 'Adresse de l\'authentification Geosource pour la synchronisation de cartes',
	);
	$form['geosource sync'] = array(
		'#type' => 'fieldset',
		'#title' => 'Options de synchronisation Geosource',
		'#collapsible' => false,
	);
	$form['geosource sync']['geosource_sync_delay'] = array(
		'#type' => 'textfield',
		'#title' => 'Intervalle de synchronisation',
		'#default_value' => variable_get('geosource_sync_delay', ''),
		'#description' => 'Délai entre deux synchronisations avec Geosource (en secondes)',
	);
	// TODO: utilisateur pour les cartes créées par la synchro
	// TODO: auteur par défaut pour les cartes créées par la synchro n'ayant pas d'email associé
	
	return system_settings_form($form);
}

function tic_geosource_sync() {
	$pageContent = array();
	$error = false;
	
	module_load_include('inc', 'tic_geosource', 'includes/tic_geosource.utils');
	
	try {
		$client = tic_geosource_load_csw_client();
		//TODO: ajouter une méthode getAll dans cswGeoClient.php
		$xml = $client->getRecordsModifiedSince(new DateTime('01-01-2000'));
		$uuids = tic_geosource_get_result_uuids($xml);
		
		// Ajouter les uuids a traiter dans le queue
		$queue = DrupalQueue::get('tic_geosource_sync');
		foreach ($uuids as $uuid) {
			$queue->createItem($uuid);
		}
	}
	catch (Exception $e) {
		$error = true;
	}
	
	drupal_set_title('Synchronisation avec Geosource');
	if ($error == false) {
		$pageContent['#markup'] = '<p>La synchronisation avec Geosource a été lancée ('.count($uuids).' fiches dans la file d\'attente)</p>';
	}
	else {
		$pageContent['#markup'] = '<p>Une erreur est survenue lors du lancement de la synchronisation avec Geosource</p>';
	}
	
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
		$dom->loadXML((string)$records);
		
		$capabilities = '<br />';
		
		$caps = $dom->getElementsByTagName('OperationsMetadata');
		foreach($caps->item(0)->getElementsByTagName('Operation') as $cap) {
			$capabilities .= '- ' . $cap->getAttribute('name') . '<br />';
		}
	} catch (Exception $e) {
		$error = true;
		$statusStr = 'ERREUR';
		$errorStr = $e->getMessage();
	}
	
	drupal_set_title('Test de la configuration Geosource');
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
		$pageContent['#markup'] .= '<p>Connexion à Geosource réussie. Liste des capabilities du serveur: ' . $capabilities . '</p>';
	}
	else {
		$pageContent['#markup'] .= '<p>Echec de la connexion à Geosource. Il semble que vos paramètres ne soient pas corrects...</p>';
		$pageContent['#markup'] .= '<h2>Informations complémentaires: </h2>'.$errorStr;
	}
	
	return drupal_render($pageContent);
}
