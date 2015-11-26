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

