<?php

/**
 * Split xml results records in array of xml records
 *
 * @param string xml
 * @return array of DOMElements
 */
function tic_geosource_split_records($xml) {
	$dom = new DOMDocument('1.0');
	$dom->loadXML((string)$xml);
	
	$records = array();
	
	// extract records, push in array
	foreach ($dom->getElementsByTagName('Record') as $rec) {
		$records[] = $rec;
	}

	return $records;
}

/**
 * Get records UUID from a CSW query result
 *
 * @param string xml
 * @return array of uuid
 */
function tic_geosource_get_result_uuids($xml) {
	$uuids = array();
	
	$dom = new DOMDocument('1.0');
	$dom->loadXML((string)$xml);
	
	foreach($dom->getElementsByTagName('BriefRecord') as $rec) {
		$uuids[] = tic_geosource_get_single_element_value($rec, 'identifier');
	}
	
	return $uuids;
}

function tic_geosource_process_record($uuid) {
	$client = tic_geosource_load_csw_client();
	$xml = $client->getRecordById($uuid);
	$dom = new DOMDocument('1.0');
	$dom->loadXML((string)$xml);
	
	tic_geosource_record_to_node($dom);
}

function tic_geosource_check_record($uuid) {
	$client = tic_geosource_load_csw_client();
	$xml = $client->getRecordById($uuid);
	$dom = new DOMDocument('1.0');
	$dom->loadXML((string)$xml);
	
	//si la ressource n'as pas de uuid elle n'existe pas
	$recordUuid = tic_geosource_get_single_element_value($dom, 'fileIdentifier', false);
	
	if ($recordUuid === false) {
		// le node n'existe pas dans Geosource
		$node = tic_geosource_get_node_by_uuid($uuid);
		if($node) {
			// Suppression ou depublication ?
			node_delete($node->nid);
			watchdog('tic_geosource', 'Suppression du node '.$uuid.' supprimé dans Geosource');
		}
	}
}

/**
 * Extract single tag value from xml
 *
 * @param DOMDocument record
 * @param string a tag name
 * @return value
 *
 */
function tic_geosource_get_single_element_value($record, $tag, $default = '') {
	$nodes = $record->getElementsByTagName($tag);
	//return $nodes->item(0)->nodeValue;
	if($nodes->length > 0) {
		return trim($nodes->item(0)->nodeValue);
	}
	
	watchdog('tic_geosource', 'Le tag '.$tag.' n\' a pas été trouvé pendant la lecture du record');
	return $default;
}

/**
 * Extract a value from Xpath
 *
 * @param DOMDocument $record
 * @param string Xpath
 * @param default value to return if nothing found
 * @return string or default value
 */
function tic_geosource_get_single_element_by_path($record, $path, $default = '') {
	$xpath = new DOMXpath($record);
	$xpath->registerNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
	$nodes = $xpath->query($path, $record);
	
	if($nodes->length > 0) {
		return trim($nodes->item(0)->nodeValue);
	}
	
	return $default;
}

/**
 * Extract multiple tag values from xml
 *
 * @param DOMDocument record
 * @param string tag name
 * @return array of values
 */
function tic_geosource_get_multiple_elements_value($record, $tag) {
	$values = array();
	
	foreach($record->getElementsByTagName($tag) as $n) {
		$values[] = $n->nodeValue;
	}
	
	return $values;
}

// TODO: fichiers ?
/**
 * Recuperation de URL carte et des fichiers associés a la ressource
 *
 * @param DOMDocument Geosource ressource
 * @return array
 */
function tic_geosource_get_record_files($record) {
	$files = array(
		'url_carte' => array(),
		'fichiers' => array(),
	);
	
	$xpath = new DOMXpath($record);
	$xpath->registerNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
	
	$debug = false;
	
	foreach ($xpath->query('//gmd:MD_Distribution/gmd:transferOptions/gmd:MD_DigitalTransferOptions/gmd:onLine') as $n) {
		$protocol = $n->getElementsByTagName('protocol');
		if($protocol->length == 0) { continue; }
		if(trim($protocol->item(0)->nodeValue) == 'OGC:WMS') {
			$key = 'url_carte';
		}
		else { $key = 'fichiers'; }
		
		$urls = $n->getElementsByTagName('URL');
		if($urls->length == 0) { continue; }
		
		$files[$key][] = array('URL' => trim($urls->item(0)->nodeValue));
	}
	
	return $files;
}

/**
 * Get the thumbnail URL from the record
 *
 * @param DOMElement record
 * @return file object or false
 */
function tic_geosource_get_record_thumbnail($record) {
	//tenter d'abord de recuperer large_thumbnail, sinon thumbnail
	
	$large = array();
	$small = array();
	$other = array();
	
	foreach ($record->getElementsByTagName('graphicOverview') as $thumbNode) {
		$desc = trim($thumbNode->getElementsByTagName('fileDescription')->item(0)->nodeValue);
		$name = trim($thumbNode->getElementsByTagName('fileName')->item(0)->nodeValue);
		$fileType = trim($thumbNode->getElementsByTagName('fileType')->item(0)->nodeValue);
		
		if ($desc == 'thumbnail') {
			$small = array(
				'uri' => $name,
				'filetype' => $fileType,
			);
		}
		elseif ($desc == 'large_thumbnail') {
			$large = array(
				'uri' => $name,
				'filetype' => $fileType,
			);
		}
		else {
			$other = array(
				'uri' => $name,
				'filetype' => $fileType,
			);
		}
	}
	
	if (!empty($large)) {
		$data = @file_get_contents($large['uri']);
		if ($data !== false) {
			$thumb = file_save_data($data, file_default_scheme() . '://');
			if ($thumb) { return $thumb; }
		}
		else {
			watchdog('tic_geosource', 'Une erreur est survenue lors de la tentative de récupération du fichier '.$large['uri']);
		}
	}
	
	if (!empty($small)) {
		$data = file_get_contents($small['uri']);
		if ($data !== false) {
			$thumb = file_save_data($data, file_default_scheme() . '://');
			return $thumb;
		}
	}
	
	if (!empty($other)) {
		$data = file_get_contents($other['uri']);
		if($data !== false) {
			$thumb = file_save_data($data, file_default_scheme() . '://');
			return $thumb;
		}
	}
	
	return false;
}


/**
 * Extraction des mots cles et themes
 * @param DOMDocument Geosource resource
 * @return array Tableau associatif themes et mots cles
 */
function tic_geosource_get_keywords_and_themes($record) {
	$ret = array();
	
	$ret['themes'] = array();
	$ret['keywords'] = array();
	
	$nodes = $record->getElementsByTagName('descriptiveKeywords');
	if (! $nodes->length > 0) {
		return $ret;
	}
	
	foreach ($nodes as $node) {
		$thesaurus = $node->getElementsByTagName('thesaurusName');
		if ($thesaurus->length > 0) {
			$key = 'themes';
		}
		else {
			$key = 'keywords';
		}
		
		$knodes = $node->getElementsByTagName('keyword');
		foreach($knodes as $k) {
			$ret[$key][] = $k->nodeValue;
		}
	}
	
	return $ret;
}

/**
 * Obtenir l'emprise geographique depuis la bbox
 *
 * @param DOMDocument record
 */
function tic_geosource_get_record_bbox_equivalent($record) {
	$west = tic_geosource_get_single_element_value($record, 'westBoundLongitude', '-180');
	$north = tic_geosource_get_single_element_value($record, 'northBoundLatitude', '-90');
	$south = tic_geosource_get_single_element_value($record, 'southBoundLatitude', '90');
	$east = tic_geosource_get_single_element_value($record, 'eastBoundLongitude', '180');
	
	return tic_geosource_bbox_to_region($north, $south, $east, $west);
}

/**
 * Extraction des infos source de données
 *
 * @param DOMDocument Geosource Record
 * @return array Tableau associatif url, description et date de source de données
 */
// TODO extraction des données via regex
function tic_geosource_get_source_info($record) {
	$infos = array(
		'url' => '',
		'date' => '',
		'text' => '',
	);
	
	$raw = tic_geosource_get_single_element_by_path($record, '//gmd:lineage/gmd:LI_Lineage/gmd:statement');
	
	$infos['text'] = htmlentities($raw);
	
	return $infos;
}

/**
 * Save or update record as drupal node
 *
 * @param DOMDocument
 */
function tic_geosource_record_to_node($record) {
	// Attributs
	$uuid = tic_geosource_get_single_element_value($record, 'fileIdentifier', false);
	
	// If no uuid found, do not continue
	if ($uuid === false) {
		watchdog('tic_geosource', 'Aucun UUID trouvé pour une fiche lors de la synchronisation avec Geosource');
		return;
	}
	
	$title = tic_geosource_get_single_element_value($record, 'title');
	$abstract = tic_geosource_get_single_element_value($record, 'abstract');
	$date = tic_geosource_get_single_element_value($record, 'date');
	$scale = tic_geosource_get_single_element_by_path($record, '//gmd:spatialResolution/gmd:MD_Resolution/gmd:equivalentScale/gmd:MD_RepresentativeFraction/gmd:denominator', '');
	$category = tic_geosource_get_single_element_value($record, 'MD_TopicCategoryCode');
	$numCarte = intval(tic_geosource_get_single_element_by_path($record, '//gmd:identifier/gmd:MD_Identifier/gmd:code'));
	$contactName = tic_geosource_get_single_element_by_path($record, '//gmd:contact/gmd:CI_ResponsibleParty/gmd:individualName');
	$contactEmail = tic_geosource_get_single_element_by_path($record, '//gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress');
	$empriseGeo = tic_geosource_get_record_bbox_equivalent($record);
	$sourceInfos = tic_geosource_get_source_info($record);
	$kt = tic_geosource_get_keywords_and_themes($record);
	$creationDate = tic_geosource_get_single_element_by_path($record, '//gmd:MD_Metadata/gmd:dateStamp');
	$updateDate = tic_geosource_get_single_element_by_path($record, '//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:date/gmd:CI_Date/gmd:date');

	$contact = false;
	if ($contactEmail != '') {
		$contact = tic_geosource_load_contact_by_email($contactEmail);
		if (!$contact) {
			if($contactName != '') {
				//create contact
				$contact = tic_geosource_create_contact($contactName, $contactEmail);
				watchdog('tic_geosource', 'Creation automatique du contact "'.$contactName.'" avec le nid "'.$contact->nid.'"');
			}
		}
	}
	
	// Fichiers
	$thumb = tic_geosource_get_record_thumbnail($record);
	
	$files = tic_geosource_get_record_files($record);
	
	// Fichiers associés
	
	$node = tic_geosource_get_node_by_uuid($uuid);
	
	if (!$node) {
		$node = new stdClass();
		
		$node->type = 'carte';
		node_object_prepare($node);
		
		$node->is_new = true;
		$node->status = 1; //publié par defaut
		$node->comment = 0; //commentaires désactivés
		$node->promote = 0;
		$node->language = 'fr';
		$node->field_uuid['und'][0]['value'] = $uuid;
		$node->field_isharvested['und'][0]['value'] = 1; // Carte récupérée de Geosource
		$node->field_statut['und'][0]['value'] = 'interne'; // Carte de geosource publiees en interne par defaut
	}
	
	// TODO: image et fichier ?
	if ($scale !== false) {
		$node->field_echelle['und'][0]['value'] = $scale;
	}
	
	$categoryTid = tic_geosource_get_tid_from_term_name('categorie', $category);
	if ($categoryTid) {
		$node->field_categorie['und'][0]['tid'] = $categoryTid;
	}
	
	// atttributs
	$node->title = $title;
	$node->field_description['und'][0]['value'] = $abstract;
	$node->field_numero_de_carte['und'][0]['value'] = $numCarte;
	$node->field_url_source_des_donnees['und'][0]['value'] = $sourceInfos['url'];
	$node->field_date_source_des_donnees['und'][0]['value'] = $sourceInfos['date'];
	$node->field_source_des_donnees['und'][0]['value'] = $sourceInfos['text'];
	$node->field_emprise_geographique['und'][0]['value'] = $empriseGeo;
	$node->field_date_de_creation['und'][0]['value'] = $creationDate;
	$node->field_date_de_mise_jour['und'][0]['value'] = $updateDate;
	
	if ($contact) {
		$node->field_auteur['und'][0]['nid'] = $contact->nid;
	}
	else {
		watchdog('tic_geosource', 'Aucun contact trouvé pour la ressource '.$title);
	}
	
	$node->field_mots_cles['und'] = array();
	foreach($kt['keywords'] as $keyword) {
		$tid = tic_geosource_get_tid_from_term_name('mots_cles', $keyword);
		if ($tid) {
			$node->field_mots_cles['und'][]['tid'] = $tid;
		}
	}
	
	$node->field_thematique['und'] = array();
	foreach($kt['themes'] as $theme) {
		$tid = tic_geosource_get_tid_from_term_name('thematique_gemet', $theme);
		if ($tid) {
			$node->field_thematique['und'][]['tid'] = $tid;
		}
	}
	
	//Fichiers
	if ($thumb) {
		$node->field_imagette['und']['0']['fid'] = $thumb->fid;
	}
	
	foreach($files['url_carte'] as $url_carte) {
		$node->field_url_carte['und'][0]['value'] = $url_carte['URL'];
	}
	
	// TODO choisir si la carte est statique ou interactive en fonction des champs récupérés
	if ($scale === '') {
		$node->field_type_de_carte['und'][0]['value'] = 'statique';
	}
	else { $node->field_type_de_carte['und'][0]['value'] = 'dynamique'; }
	
	// disable push to geosource
	$disabled = &drupal_static('tic_geosource_disable_geosource_push');
	$disabled = true;
	
	try {
		node_save($node);
	}
	catch (Exception $e) {
		watchdog('tic_geosource', 'Synchronisation de la carte '. $node->title . ' echoue ' .$e->getMessage());
	}
	$disabled = false;
}

/**
 * Get drupal node by geosource uuid
 *
 * @param string uuid
 * @return stdClass Drupal node or false
 */
function tic_geosource_get_node_by_uuid($uuid) {
	
	$query = new EntityFieldQuery();
	
	$query->entityCondition('entity_type', 'node')
		->entityCondition('bundle', 'carte')
		->fieldCondition('field_uuid', 'value', $uuid, '=')
	;
	
	$result = $query->execute();
	
	if (!is_array($result)) { return false; }
	
	if (array_key_exists('node', $result)) {
		$obj = array_pop($result['node']);
		return node_load($obj->nid, $obj->vid);
	}
	
	return false;
}

/**
 * Get vocabulary term id (save the term if it does not exists)
 *
 * @param string vocabulary
 * @param string Term name
 * @param bool save the term if it does not exists
 * @return int term ID or false
 */
function tic_geosource_get_tid_from_term_name($vocabulary, $term_name, $save = false) {
	//$vocabulary = 'thematique_gemet';
	$arr_terms = taxonomy_get_term_by_name($term_name, $vocabulary);
	if (!empty($arr_terms)) {
		$arr_terms = array_values($arr_terms);
		$tid = $arr_terms[0]->tid;
	}
	else {
		if ($save == false) { return false; }
		
		$vobj = taxonomy_vocabulary_machine_name_load($vocabulary);
		$term = new stdClass();
		$term->name = $term_name;
		$term->vid = $vobj->vid;
		taxonomy_term_save($term);
		$tid = $term->tid;
	}
	return $tid;
}

function tic_geosource_load_contact_by_email($email) {
	$query = new EntityFieldQuery();
	
	$query->entityCondition('entity_type', 'node')
		->entityCondition('bundle', 'contact')
		->fieldCondition('field_adresse_email', 'email', $email, '=')
	;
	
	$result = $query->execute();
	
	if(!is_array($result)) { return false; }
	
	if(array_key_exists('node', $result)) {
		$obj = array_pop($result['node']);
		return node_load($obj->nid, $obj->vid);
	}
	
	return false;
}

function tic_geosource_create_contact($name, $email) {
	global $user;
	
	$contact = new StdClass();
	$contact->type = 'contact';
	$contact->title = $name;
	$contact->uid = $user->uid;
	$contact->language = 'fr';
	
	//TODO: vérifier que le node a bien été sauvé
	node_save($contact);
	$node_wrapper = entity_metadata_wrapper('node', $contact);
	$node_wrapper->field_adresse_email->set($email);
	$node_wrapper->save();
	
	return $contact;
}

function tic_geosource_load_thesaurus() {
	$client = tic_geosource_load_csw_client();
	
	$resources = $client->getGeoThemeThesaurus();
	
	foreach($resources as $resource) {
		// Categories
		if (isset($resource['topicCats'])) {
			// feed category taxo
			foreach($resource['topicCats'] as $category) {
				if(isset($category['@name'])) {
					tic_geosource_get_tid_from_term_name('categorie', $category['@name']);
				}
			}
		}
		
		// Themes
		if (isset($resource['inspireThemes'])) {
			// feed inspire Themes taxo
			foreach($resource['inspireThemes'] as $theme) {
				if(isset($category['@name'])) {
					tic_geosource_get_tid_from_term_name('thematique_gemet', $theme['@name']);
				}
			}
		}
		
		// Mots cles
		if (isset($resource['keywords'])) {
			// feed keywords taxo
			foreach($resource['keywords'] as $keyword) {
				if (isset($keyword['@name'])) {
					tic_geosource_get_tid_from_term_name('mots_cles', $keyword['@name']);
				}
			}
		}
	}
}
