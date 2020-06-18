<?php

/**
* Implements hooks_drush_command().
*/
function tic_theme_hdf_update_drush_command() {

	$items = array();
	$items['import-update-theme-hdf'] = array(
		'description' => dt('Command update thematic HdF via Excel Import.'),
		'aliases'     => array('ihdfu'),
		'callback'    => 'tic_theme_hdf_update_import',
		'bootstrap'   => DRUSH_BOOTSTRAP_DRUSH,
		'arguments'   => array(
			'path'	=> dt('The path where the file to be imported is located.'),
		),	
	); 
	return $items;
}


/**
* Implements of hook_drush_help().
*/
function tic_theme_hdf_update_drush_help($section) {

	switch($section) {
		case 'drush:import-update-theme-hdf':
		return dt('This command update thematic HdF via Excel Import.');
	}
}


function tic_theme_hdf_update_import($path) {

	if(!empty($path) && file_exists($path)) {
		module_load_include('inc', 'phpexcel');

		// lecture du fichier excel
		$result = phpexcel_import($path);

		// Récupération du vid de la taxonomie
		$vocabulary_name = 'thematique_hdf';
		$vocab = taxonomy_vocabulary_machine_name_load($vocabulary_name);
		// Récupération de tous les termes liés à la taxonomie (thematique_hdf)
		$terms = taxonomy_get_tree($vocab->vid);
		// contruction tableau nomTerme = tid
		$termsByTaxo = array();
		foreach($terms as $term) {
			
			$termsByTaxo[$term->tid] = strtolower($term->name);
		}

		if(is_array($result)) {
			$countImportMap = 0;
			foreach($result as $value) {
				foreach($value as $map) {
					
					// On parcourt le tableau $termsByTaxo pour récupérer le tid du terme de taxo
					$tid = array_search(strtolower($map['theme_HdF']), $termsByTaxo);

					// sur chaque ligne on update la bdd (table jointure field_data_field_thematique_hdf)
					if($map['nid'] && !empty($map['nid']) && !empty($tid) ) {
						$node = node_load($map['nid']);
						//dump($map['nid']);
						
						if($node) {
							$node->field_thematique_hdf['und'][0]['tid'] = $tid;
							node_save($node);
							$countImportMap++;
							}
					}
				}

			}
			drupal_set_message(t('Success: @count updated map(s).', array('@count' => $countImportMap)));	
		}

	}
	else {
		drupal_set_message(t('Error: Please add path of the xls file or check the path of the xls file to import.'));
	}
}


