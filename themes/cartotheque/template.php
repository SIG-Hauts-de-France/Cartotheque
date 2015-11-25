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


function cartotheque_form_search_block_form_alter(&$form, &$form_state, $form_id) {
	//var_dump( $form ); die();
	$form['search_block_form']['#attributes']['placeholder'] = t('Search');
	$form['search_block_form']['#attributes']['class'][] = 'form-control';
	
}
