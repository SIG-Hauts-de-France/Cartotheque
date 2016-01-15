<?php

include_once dirname(__FILE__) . '/pager.func.php';

/**
 * Implements hook_html_head_alter().
 * This will overwrite the default meta character type tag with HTML5 version.
 */
function cartotheque_preprocess_html(&$variables) {
	drupal_add_css('https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,300,600,300italic,700', array('type' => 'external'));

	// Chosen pour les input select
	drupal_add_js(drupal_get_path('theme', 'cartotheque') . '/js/chosen/chosen.jquery.min.js', 'file');
	drupal_add_js(drupal_get_path('theme', 'cartotheque') . '/js/jquery.sticky.js', 'file');
	drupal_add_js(drupal_get_path('theme', 'cartotheque') . '/js/handheld_detect.js', 'file');
	drupal_add_css(drupal_get_path('theme', 'cartotheque') . '/js/chosen/chosen.min.css', array( 'group' => CSS_THEME, 'type' => 'file'));

	if ($_SESSION['tic_user_has_searched']) {
		drupal_add_js('var openAccordion = true;', 'inline');
	}
	else {
		drupal_add_js('var openAccordion = false;', 'inline');
	}
}

/**
 * Override or insert variables into the page template.
 */

function cartotheque_preprocess_page(&$variables) {
	cartotheque_retrieve_search_params();
  
/*
  if($variables['page']['sidebar_first'] && $variables['page']['sidebar_second']){
	$variables['contentclass'] = 'col-sm-6 col-sm-push-3';
	$variables['firstsidebarpush'] = 'col-sm-pull-6';
	}
  elseif($variables['page']['sidebar_first'] || $variables['page']['sidebar_second']){
	if($variables['page']['sidebar_first']){
		$variables['contentclass'] = 'col-sm-9 col-sm-push-3';
		$variables['firstsidebarpush'] = 'col-sm-pull-9';		
		}
	if($variables['page']['sidebar_second']){
		$variables['contentclass'] = 'col-sm-9';
		}		
	}
  else{
	$variables['contentclass'] = 'col-sm-12';
	}
*/
	
/*	
	
    $variables['copyright'] = theme_get_setting('copyright', 'wine');
  if (!$variables['copyright']) {
	$variables['copyright'] = 'edit copyright text from theme setting page.';
  }
  
  $variables['tagline_setting'] = theme_get_setting('tagline_setting', 'wine');
  if (!$variables['tagline_setting']) {
	$variables['tagline_setting'] = 'Tag Line';
  }
 */ 	

}


function cartotheque_preprocess_select(&$variables) {
  $variables['element']['#attributes']['class'][] = 'form-control';
}

function cartotheque_preprocess_textfield(&$variables) {
	$variables['element']['#attributes']['class'][] = 'form-control';
	$variables['element']['#attributes']['placeholder'] = t('Search');
	$variables['element']['#field_prefix'] = 'Help';
}

function cartotheque_form_element(&$variables) {
	$element = &$variables['element'];

	$element += array(
			'#title_display' => 'before',
			);

	// Add element #id for #type 'item'.
	if (isset($element['#markup']) && !empty($element['#id'])) {
		$attributes['id'] = $element['#id'];
	}
	// Add element's #type and #name as class to aid with JS/CSS selectors.
	$attributes['class'] = array('form-item');
	if (!empty($element['#type'])) {
		$attributes['class'][] = 'form-type-' . strtr($element['#type'], '_', '-');
	}
	if (!empty($element['#name'])) {
		$attributes['class'][] = 'form-item-' . strtr($element['#name'], array(' ' => '-', '_' => '-', '[' => '-', ']' => ''));
	}
	// Add a class for disabled elements to facilitate cross-browser styling.
	if (!empty($element['#attributes']['disabled'])) {
		$attributes['class'][] = 'form-disabled';
	}
	$output = '<div' . drupal_attributes($attributes) . '>' . "\n";

	// If #title is not set, we don't display any label or required marker.
	if (!isset($element['#title'])) {
		$element['#title_display'] = 'none';
	}
	$prefix = isset($element['#field_prefix']) ? '<span class="field-prefix">' . $element['#field_prefix'] . '</span> ' : '';
	$suffix = isset($element['#field_suffix']) ? ' <span class="field-suffix">' . $element['#field_suffix'] . '</span>' : '';

	//if(array_key_exists('#name',$element) && $element['#name']=="combine") {
	if(array_key_exists('#name',$element) && $element['#name']=="pgsql_combine_filter_views") {
		$prefix = '<span class="field-prefix input-group-addon search" id="basic-addon-search"></span>';
	}

	switch ($element['#title_display']) {
		case 'before':
		case 'invisible':
			$output .= ' ' . theme('form_element_label', $variables);
			$output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
			break;

		case 'after':
			$output .= ' ' . $prefix . $element['#children'] . $suffix;
			$output .= ' ' . theme('form_element_label', $variables) . "\n";
			break;

		case 'none':
		case 'attribute':
			// Output no label and no required marker, only the children.
			$output .= ' <div class="input-group">' . $prefix . $element['#children'] . $suffix . "</div>\n";
			break;
	}

	if (!empty($element['#description'])) {
		$output .= '<div class="description">' . $element['#description'] . "</div>\n";
	}

	$output .= "</div>\n";



	return $output;
}

function cartotheque_preprocess_views_exposed_form(&$vars) {
	//dsm($vars['widgets']);
}
function cartotheque_preprocess_views_view(&$vars) {
	//dsm( $vars);
}

function cartotheque_preprocess_node(&$vars) {
	global $base_url;
	
	$vars['download_count'] = '';
	$vars['stats_total_count'] = '';
	
	if ($vars['node']->type == 'carte') {
		//Collections
		//dsm($vars['field_collections']); die();
		$vars['collections'] = '<table class="table table-hover">';
		$vars['collections_count'] = 0;
		$collections = $vars['field_collections'];
		
		$num = 1;
		foreach($collections as $col) {
			//var_dump($col); die();
			$vars['collections'] .= '<tr><th scope="row">'.$num.'</th><td><a href="'.theme_get_setting('cartotheque_map_list_url').'&field_collections_tid[]='.$col['taxonomy_term']->tid.'">'.$col['taxonomy_term']->name.'</a>';
			$num++;
		}
		
		$vars['collections'] .= '</table>';
		// Conservation du nombre de collections pour faciliter l'affichage
		$vars['collections_count'] = --$num;
		
		// Lien vers la recherche sur categorie
		if (isset($vars['field_categorie'][0]['taxonomy_term'])) {
			$term = $vars['field_categorie'][0]['taxonomy_term'];
			$vars['categoryLink'] = '<a href="'.$base_url . theme_get_setting('cartotheque_map_list_url').'&field_categorie_tid[]='.$term->tid.'">'.$term->name.'</a>';
			
			$vars['categoryName'] = $term->name;
		}
		else {
			$vars['categoryLink'] = '';
		}
		
		// Liens vers la recherche thematiques
		//var_dump($vars['field_thematique']); die();
		$vars['thematiquesLinks'] = '';
		if (isset($vars['field_thematique'])) {
			foreach ($vars['field_thematique'] as $thematique) {
				//var_dump($thematique); die();
				$term = $thematique['taxonomy_term'];
				$vars['thematiquesLinks'] .= '<a href="'.$base_url. theme_get_setting('cartotheque_map_list_url').'&field_thematique_tid[]='.$term->tid.'">'.$term->name.'</a> ';
			}
		}
		
		$vars['keywordsLinks'] = '';
		if (isset($vars['field_mots_cles'])) {
			foreach ($vars['field_mots_cles'] as $keyword) {
				$term = $keyword['taxonomy_term'];
				$vars['keywordsLinks'] .= ' <a href="'. $base_url. theme_get_setting('cartotheque_map_list_url').'&field_mots_cles_tid[]='.$term->tid.'">'.$term->name.'</a>,';
			}

			$vars['keywordsLinks'] = trim($vars['keywordsLinks'], ',');
		}
		
		$vars['inspireKeywordsLinks'] = '';
		if (isset($vars['field_mots_cles_thesaurus'])) {
			foreach ($vars['field_mots_cles_thesaurus'] as $keyword) {
				$term = $keyword['taxonomy_term'];
				$vars['inspireKeywordsLinks'] .= ' <a href="'. $base_url. theme_get_setting('cartotheque_map_list_url').'&field_mots_cles_thesaurus_tid[]='.$term->tid.'">'.$term->name.'</a>,';
			}

			$vars['inspireKeywordsLinks'] = trim($vars['inspireKeywordsLinks'], ',');
		}

		$vars['ressourcesAssociees'] = '';
		if (isset($vars['field_ressources_associes'])) {
			foreach ($vars['field_ressources_associes'] as $res) {
				$vars['ressourcesAssociees'] .= '<a target="about:_blank" href="'.$res['value'].'">'.$res['value'].'</a> ';
			}
		}
		
		// Statistiques d'accès
		if (function_exists('statistics_get')) {
			$stats = statistics_get($vars['node']->nid);
			if ($stats === false) {
				$vars['stats_total_count'] = "0";
			}
			else {
				$vars['stats_total_count'] = $stats['totalcount'];
			}
		}
		
		if (function_exists('tic_carto_count_get_node_download_count')) {
			$downloadCount = tic_carto_count_get_node_download_count($vars['node']->nid);
			$vars['download_count'] = $downloadCount;
		}
	}
	else {
		// Ne pas afficher le nombre de vues pour les autres types de contenus
		$vars['content']['links']['statistics'] = array();
	}
}

//Images responsive
function cartotheque_preprocess_image_style(&$vars) {
	$vars['attributes']['class'][] = 'img-responsive';
}


function cartotheque_menu_tree(array $variables) {
	if($variables['theme_hook_original']=="menu_tree__menu_advanced_search_menu")
		return '<ul class="nav navbar-nav searchMore">' . $variables['tree'] . '</ul>';
	return '<ul class="nav navbar-nav">' . $variables['tree'] . '</ul>';
}
function cartotheque_menu_link(array $variables) {
	$element = $variables['element'];
	$sub_menu = '';

	$switch = $element['#original_link']['has_children'];

	if ($element['#below']) {
		$sub_menu = drupal_render($element['#below']);
	}
	$element['#localized_options']['html'] = TRUE;
	#This adds the span only to the parent Title: If you need the span on all text Titles Just remove the if else statement and leave  $linktext = '<span class="your_class">' . $element['#title'] . '</span>'; : 
	if($switch == 1) {
		$linktext = '<span class="your_class">' . $element['#title'] . '</span>';
	} else {
		$linktext = $element['#title'];
	}
	$output = l($linktext, $element['#href'], $element['#localized_options']);
	if($element['#theme']=="menu_link__menu_advanced_search_menu") {
		return '<li' . drupal_attributes($element['#attributes']) . '><span></span>' . $output . $sub_menu . "</li>\n";
	}
	return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
}

/**
 * Recupération et conservation des paramètres de recherche
 *
 */
function cartotheque_retrieve_search_params() {
	if (!isset($_SESSION['tic_user_has_searched'])) {
		$_SESSION['tic_user_has_searched'] = false;
	}

	// Reset search on front page
	if (drupal_is_front_page()) {
		$_SESSION['tic_user_has_searched'] = false;
		$_SESSION['tic_search_params'] = serialize(array('field_cartotheque_value' => 'NPdCP'));
		return;
	}

	$params = drupal_get_query_parameters();
	
	// TODO: workaround date params

	// params to be saved
	$searchParams = array(
		'combine',
		'pgsql_combine_filter_views',
		'field_emprise_geographique_value',
		'field_mots_cles_tid',
		'field_thematique_tid',
		'field_collections_tid',
		'field_categorie_tid',
		'combine_1',
		'pgsql_combine_filter_views',
		'field_type_de_carte_value',
		'sort_by',
		'sort_order',
		'field_cartotheque_value',
		'field_date_de_creation_value',
		'field_date_de_creation_value_op',
		'sort_bef_combine',
		'items_per_page',
	);
	
	if (isset($_SESSION['tic_search_params'])) {
		$savedParams = unserialize($_SESSION['tic_search_params']);
	}
	else { $savedParams = array(); }
	
	// Reset des params sauvés si nouvelle recherche
	if (current_path() == 'map-list') {
		$savedParams = array();
	}
	
	$hasSearched = false;
	foreach($params as $p => $v) {
		if (in_array($p, $searchParams)) {
			if ($p != 'field_cartotheque_value') {
				$hasSearched = true;
			}
			$savedParams[$p] = $v;
		}
	}
	

	if ($_SESSION['tic_user_has_searched'] == false) {
		$_SESSION['tic_user_has_searched'] = $hasSearched;
	}

	$_SESSION['tic_search_params'] = serialize($savedParams);
}

/**
 * Generation de l'url de recherche avec les paramètres
 */
function cartotheque_generate_search_url() {
	//cartotheque_retrieve_search_params();
	
	$url = theme_get_setting('cartotheque_map_list_url');
	
	if (isset($_SESSION['tic_search_params'])) {
		$savedParams = unserialize($_SESSION['tic_search_params']);
	}
	else { $savedParams = array(); }
	
	// Recherche par defaut sur NPDcP
	if (!isset($savedParams['field_cartotheque_value'])) {
		$savedParams['field_cartotheque_value'] = Array('NPdCP');
	}

	//Tri par défaut
	if (!isset($savedParams['sort_bef_combine'])) {
		$savedParams['sort_bef_combine'] = 'timestamp DESC';
	}
	
	foreach($savedParams as $p => $v) {
		if (is_array($v)) {
			foreach($v as $val) {
				$url .= '&'.$p.'[]'.'='.$val;
			}
		}
		else { $url .= '&'.$p.'='.$v; }
	}
	
	return $url;
}
