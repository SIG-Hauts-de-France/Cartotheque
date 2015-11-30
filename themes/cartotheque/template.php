<?php
/**
 * Implements hook_html_head_alter().
 * This will overwrite the default meta character type tag with HTML5 version.
 */
function cartotheque_preprocess_html(&$variables) {
  drupal_add_css('https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,300,600,300italic,700', array('type' => 'external'));
} 
 
/**
 * Override or insert variables into the page template.
 */

function cartotheque_preprocess_page(&$variables) {
  
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

	if($element['#name']=="combine")
		$prefix = '<span class="field-prefix input-group-addon search" id="basic-addon-search"></span>';

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
			$vars['collections'] .= '<tr><th scope="row">'.$num.'</th><td><a href="/?q=map-list&field_collections_tid='.$col['taxonomy_term']->tid.'">'.$col['taxonomy_term']->name.'</a>';
			$num++;
		}
		
		$vars['collections'] .= '</table>';
		// Conservation du nombre de collections pour faciliter l'affichage
		$vars['collections_count'] = --$num;
		
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
	{
		// Ne pas afficher le nombre de vues
		$vars['content']['links']['statistics'] = array();
	}
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
