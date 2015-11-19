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
	
	foreach($record->getElementsByTagName('graphicOverview') as $thumbNode) {
		$desc = trim($thumbNode->getElementsByTagName('fileDescription')->item(0)->nodeValue);
		$name = trim($thumbNode->getElementsByTagName('fileName')->item(0)->nodeValue);
		$fileType = trim($thumbNode->getElementsByTagName('fileType')->item(0)->nodeValue);

		var_dump($desc);
		
		if($desc == 'thumbnail') {
			$small = array(
				'uri' => $name,
				'filetype' => $fileType,
			);
		}
		elseif($desc == 'large_thumbnail') {
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
	
	if(!empty($large)) {
		$data = file_get_contents($large['uri']);
		if($data !== false) {
			$thumb = file_save_data($data, file_default_scheme() . '://');
			if($thumb) { return $thumb; }
		}
	}
	
	if(!empty($small)) {
		$data = file_get_contents($small['uri']);
		if($data !== false) {
			$thumb = file_save_data($data, file_default_scheme() . '://');
			return $thumb;
		}
	}
	
	if(!empty($other)) {
		$data = file_get_contents($other['uri']);
		if($data !== false) {
			$thumb = file_save_data($data, file_default_scheme() . '://');
			return $thumb;
		}
	}

	return false;
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
	if($uuid === false) {
		watchdog('tic_geosource', 'Aucun UUID trouvé pour une fiche lors de la synchronisation avec Geosource');
		return;
	}
	
	$title = tic_geosource_get_single_element_value($record, 'title');
	$abstract = tic_geosource_get_single_element_value($record, 'abstract');
	$date = tic_geosource_get_single_element_value($record, 'date');
	$keywords = tic_geosource_get_multiple_elements_value($record, 'keyword');
	$scale = tic_geosource_get_single_element_by_path($record, '//gmd:spatialResolution/gmd:MD_Resolution/gmd:equivalentScale/gmd:MD_RepresentativeFraction/gmd:denominator');
	$category = tic_geosource_get_single_element_value($record, 'MD_TopicCategoryCode');
	$numCarte = tic_geosource_get_single_element_by_path($record, '//gmd:referenceSystemInfo/gmd:MD_ReferenceSystem/gmd:referenceSystemIdentifier/gmd:RS_Identifier/gmd:code');
	
	//Fichiers
	$thumb = tic_geosource_get_record_thumbnail($record);
	
	$node = tic_geosource_get_node_by_uuid($uuid);
	
	if(!$node) {
		$node = new stdClass();
		
		$node->type = 'carte';
		node_object_prepare($node);
		
		$node->is_new = true;
		$node->status = 1; //publié par defaut
		$node->comment = 0; //commentaires désactivés
		$node->promote = 0;
		$node->field_uuid['und'][0]['value'] = $uuid;
		
		//TODO: gérer l'auteur; créer si besoin
	}
	
	// TODO: date de realisation
	// date de mise a jour
	// images ?
	// url carte
	// source des données
	// url source des données
	// echelle
	// emprise geographique (depuis la bbox)
	// thematique et catégories
	
	// atttributs
	$node->title = $title;
	$node->field_description['und'][0]['value'] = $abstract;
	$node->field_numero_de_carte['und'][0]['value'] = $numCarte;
	$node->field_echelle['und'][0]['value'] = $scale;
	$node->field_categorie['und'][0]['value'] = $category;
	
	$node->field_mots_cles['und'] = array();
	foreach($keywords as $keyword) {
		$tid = tic_geosource_get_tid_from_term_name($keyword);
		$node->field_mots_cles['und'][]['tid'] = $tid;
	}
	
	//Fichiers
	if($thumb) {
		$node->field_imagette['und']['0']['fid'] = $thumb->fid;
	}
	
	// disable push to geosource
	$disabled = &drupal_static('tic_geosource_disable_geosource_push');
	$disabled = true;
	node_save($node);
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

	if(!is_array($result)) { return false; }
	
	if(array_key_exists('node', $result)) {
		$obj = array_pop($result['node']);
		return node_load($obj->nid, $obj->vid);
	}
	
	return false;
}

/**
 * Get vocabulary term id (save the term if it does not exists)
 *
 * @param string Term name
 * @return int term ID
 */
function tic_geosource_get_tid_from_term_name($term_name) {
	$vocabulary = 'th_matique_gemet';
	$arr_terms = taxonomy_get_term_by_name($term_name, $vocabulary);
	if (!empty($arr_terms)) {
		$arr_terms = array_values($arr_terms);
		$tid = $arr_terms[0]->tid;
	}
	else {
		$vobj = taxonomy_vocabulary_machine_name_load($vocabulary);
		$term = new stdClass();
		$term->name = $term_name;
		$term->vid = $vobj->vid;
		taxonomy_term_save($term);
		$tid = $term->tid;
	}
	return $tid;
}
