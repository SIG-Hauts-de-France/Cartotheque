<?php

/**
 * @file
 * Administrative page callbacks for the tic_carto_count module.
 */

/**
 * Page callback for the view download counts page.
 */
function tic_carto_count_view_page($arg = NULL) {
  drupal_set_title(variable_get('tic_carto_count_view_page_title', 'Download Counts'));
  $total_downloads = 0;
  $total_views = 0;
  $colspan = 0;
  $item = 1;
  $limit = (int) variable_get('tic_carto_count_view_page_limit', 0);
  $items_per_page = (int) variable_get('tic_carto_count_view_page_items', 0);
  $page_header = variable_get('tic_carto_count_view_page_header', '');
  $page_footer = variable_get('tic_carto_count_view_page_footer', '');
  $output = '<div id="download-count-page">';

  $header = array(
    array(
      'data' => t('#'),
    ),
    array(
      'data' => t('Download count'),
      'field' => 'count',
      'sort' => 'desc',
    ),
    array(
      'data' => t('Visu. count'),
      'field' => 'count',
      'sort' => 'desc',
    ),
    array(
      'data' => t('Entity ID'),
      'field' => 'nid',
    ),
    array(
      'data' => t('Map number'),
      'field' => 'field_numero_de_carte_value',
    ),
  );

/*
  $query = db_select('tic_carto_count', 'cc')
    ->fields('cc', array('dcid', 'count'))
    ->fields('n', array('nid', 'title'))
    ->fields('nc', array('totalcount'))
	;
  $query->join('node', 'n', 'cc.nid = n.nid');
  $query->join('node_counter', 'nc', 'cc.nid = nc.nid');
*/

  $query = db_select('node', 'n')
    ->fields('n', array('nid', 'title'))
    ->fields('cc', array('dcid', 'count'))
    ->fields('nc', array('totalcount'))
    ->fields('nfc', array('field_numero_de_carte_value'))

	;
  $query->leftJoin('tic_carto_count', 'cc', 'cc.nid = n.nid');
  $query->leftJoin('node_counter', 'nc', 'nc.nid = n.nid');
  $query->leftJoin('field_data_field_numero_de_carte', 'nfc', 'nfc.entity_id = n.nid');



  if ($limit > 0) {
    $query->range(0, $limit);
  }

  if ($items_per_page > 0) {
    $query = $query->extend('PagerDefault')->limit($items_per_page);
  }
  $query = $query->extend('TableSort')->orderByHeader($header);

  $rows = array();
  $result = $query->execute();
  foreach ($result as $node) {
    $row = array();
    $row[] = $item;
    $row[] = number_format($node->count);
    $row[] = number_format($node->totalcount);
    $row[] = $node->nid;
    $row[] = $node->field_numero_de_carte_value;
    $row[] = $node->title;
    if (user_access('reset download counts')) {
      $row[] = l(t('Reset'), 'admin/reports/download-count/' . $node->nid . '/reset');
    }
    $rows[] = $row;
    $item++;
    $total_downloads += intval($node->count);
    $total_views += intval($node->totalcount);
    $colspan = count($rows[0]) - count($header);
  }

  if (empty($rows)) {
    $rows[] = array(array(
        'data' => t('No files have been downloaded.'),
        'colspan' => '9',
      ));
  }

  if ($colspan > 1) {
    $header[] = array(
      'data' => t('Actions'),
      'colspan' => $colspan,
      'id' => 'actions',
    );
    drupal_add_css(drupal_get_path('module', 'tic_carto_count') . '/tic_carto_count.css');
  }
  if (!empty($page_header['value'])) {
    $output .= '<div id="download-count-header">' . check_markup($page_header['value'], $page_header['format']) . '</div>';
  }
  $output .= '<div id="download-count-total-top">'; 
  $output .= l(t('Export'), 'admin/reports/download-count/export', array('attributes' => array('style' => 'float:right;')));
  $output .= t('Current page') . ' --- ' .  t('Total Downloads:') . ' ' . number_format($total_downloads) . ' / ' . t('Total Views:') . ' ' . number_format($total_views) 
		. '</div>';
  $output .= theme('table', array('header' => $header, 'rows' => $rows, 'attributes' => array('id' => 'download-count-table')));
  $output .= '<div id="download-count-total-bottom">' . t('Current page') . ' --- ' .  t('Total Downloads:') . ' ' . number_format($total_downloads) . ' / ' . t('Total Views:') . ' ' . number_format($total_views) . '</div>';
  if ($items_per_page > 0) {
    $output .= theme('pager', array('tags' => array()));
  }
  if (!empty($page_footer['value'])) {
    $output .= '<div id="download-count-footer">' . check_markup($page_footer['value'], $page_footer['format']) . '</div>';
  }
  $output .= '</div>';

  return $output;
}

/**
 * Download count reset function.
 */
function tic_carto_count_reset_form($form, $form_state, $dc_entry = NULL) {
  $form = array();
  if ($dc_entry != 'all') {
    $form['dcid'] = array(
      '#type' => 'value',
      '#value' => $dc_entry->dcid,
    );
    $form['nid'] = array(
      '#type' => 'value',
      '#value' => $dc_entry->nid,
    );
    $form['title'] = array(
      '#type' => 'value',
      '#value' => $dc_entry->title,
    );
    return confirm_form(
      $form,
      t('Are you sure you want to reset the download count for %filename ?', array('%filename' => $dc_entry->title)),
      'admin/reports/download-count',
      t('This action cannot be undone.'),
      t('Reset'),
      t('Cancel')
    );
  }
  else {
    $form['dcid'] = array(
      '#type' => 'value',
      '#value' => 'all',
    );
    return confirm_form(
      $form,
      t('Are you sure you want to reset all download counts?'),
      'admin/reports/download-count',
      t('This action cannot be undone.'),
      t('Reset All'),
      t('Cancel')
    );
  }
}

/**
 * Implements hook_submit().
 */
function tic_carto_count_reset_form_submit($form, &$form_state) {
  $result = NULL;
  if ($form['dcid']['#value'] == 'all') {

    $result = db_truncate('tic_carto_count')->execute();
    if ($result) {
      drupal_set_message(t('All download counts have been reset.'));
      watchdog('tic_carto_count', 'All download counts have been reset.', array(), WATCHDOG_NOTICE);
    }
    else {
      drupal_set_message(t('Unable to reset all download counts.'), 'error');
      watchdog('tic_carto_count', 'Unable to reset all download counts.', array(), WATCHDOG_ERROR);
    }
  }
  else {
    $result = db_update('tic_carto_count')
      ->fields(['count' => 0])
      ->condition('nid', $form['nid']['#value'])
      ->execute();
    if ($result) {
      drupal_set_message(t('Download count for %title was reset.', array('%title' => $form['title']['#value'])));
      watchdog('tic_carto_count', 'Download count for %title was reset.', array('%filename' => $form['title']['#value']), WATCHDOG_NOTICE);
    }
    else {
      drupal_set_message(t('Unable to reset download count for %title.', array('%title' => $form['title']['#value'])), 'error');
      watchdog('tic_carto_count', 'Unable to reset download count for %title.', array('%filename' => $form['title']['#value']), WATCHDOG_ERROR);
    }
  }
  $form_state['redirect'] = 'admin/reports/download-count';
}

