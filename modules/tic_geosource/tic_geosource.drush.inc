<?php

/**
 * Implementation of hook_drush_command().
 */
function tic_geosource_drush_command() {
        $items = array();
        $items['tic-geosource-map-uuid'] = array(
                'callback' => 'tic_geosource_manage_map_uuid',  // Callback function
                'description' => 'Drush command to manage maps nid.',
                'aliases' => array('tic-map-uuid'), // alias of command
                'examples' => array(      // List these example when user types : drush help nc
                        'List maps uuid' => 'drush tic-map-uuid list',
                        'Reset maps uuid' => 'drush tic-map-uuid reset',
                ),
        );
        return $items;
}

/*
 * Callback function for tic_geosource_nid_drush_command().
 */
function tic_geosource_manage_map_uuid() {
        // Get arguments passed in command, Ex: drush nc page blog
        $args = func_get_args();
        if ($args) {
		switch($args[0]) {
			case 'list':
				drush_print("Liste des des maps synchronisées");
				list_maps_nid();
				break;
			case 'reset':
			    if ($args[1]) {
                    drush_print("Réinitialisation de l'uuid de la map " . $args[1]);
                    reset_maps_uuid($args[1]);
                } else {
                    drush_print("Réinitialisation des uuids de maps");
                    reset_maps_uuid();
                }
				break;
			default:
				drush_print("Commande inconnue, liste des cartes synchronisées");
				list_maps_nid();
		}	
        }
        // If no type passed then return total count
        else {
                drush_print('Aucune commande passée, liste des cartes synchronisées');
		list_maps_nid();
        }
}

function list_maps_nid() {
	$ret = db_query("select nid from node where nid in (select entity_id from field_data_field_uuid) and type = 'carte';");
	$cpt = 0;

        foreach($ret as $r) {
        	$node = node_load($r->nid);
		drush_print("nid : ".$node->nid);
		$cpt++;
	}

	drush_print("Nombre de maps synchronisées : ".$cpt);
}

function reset_maps_uuid($nid = null) {
    $ret = "";
    if ($nid) {
        $ret = db_query("select nid from node where nid = " . $nid . ";");
    } else {
        $ret = db_query("select nid from node where nid in (select entity_id from field_data_field_uuid) and type = 'carte';");
    }
	$cpt = 0;

	foreach($ret as $r) {
		db_query("delete from field_data_field_uuid where entity_id = ".$r->nid.";");
		drush_print("Map réinitialisée : ".$r->nid);
		$cpt++;
	}

	drush_print("Nombre de maps réinitialisées : ".$cpt);
}
