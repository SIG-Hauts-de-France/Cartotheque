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
 * Extract single tag value from xml
 *
 * @param DOMElement record
 * @param string a tag name
 * @return value
 *
 * TODO: default value if tag not found
 */
function tic_geosource_get_single_element_value($record, $tag, $default = '') {
	$nodes = $record->getElementsByTagName($tag);
	return $nodes->item(0)->nodeValue;
}

function tic_geosource_get_multiple_elements_value($record, $tag) {
	$values = array();
	
	foreach($record->getElementsByTagName($tag) as $n) {
		$values[] = $n->nodeValue;
	}
	
	return array();
}

/**
 * Save or update record as drupal node
 *
 * @param DOMElement
 */
function tic_geosource_record_to_node($record) {
	$uuid = tic_geosource_get_single_element_value($record, 'identifier');
	$title = tic_geosource_get_single_element_value($record, 'title');
	$abstract = tic_geosource_get_single_element_value($record, 'abstract');
	$date = tic_geosource_get_single_element_value($record, 'date');
	$subjects = tic_geosource_get_multiple_elements_value($record, 'subject');
	
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
	
	$node->title = $title;
	$node->description['und'][0]['value'] = $abstract;
	
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
	
	if(is_array($result['node'])) {
		$obj = array_pop($result['node']);
		return node_load($obj->nid, $obj->vid);
	}
	
	return false;
}
