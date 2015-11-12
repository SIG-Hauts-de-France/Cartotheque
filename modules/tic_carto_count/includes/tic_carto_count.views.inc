<?php

/**
 * @file
 * Provide views data for the tic_carto_count.module.
 */

/**
 * Implements hook_views_data().
 */
function tic_carto_count_views_data() {

  // base table
  $data['tic_carto_count']['table']['group'] = t('Download history');
  $data['tic_carto_count']['table']['base'] = array(
    'field' => 'dcid',
    'title' => t('Download history'),
    'help' => t('Download history of download count module managed file downloads.'),
  );

  // join for files table
  $data['tic_carto_count']['table']['join']['node'] = array(
      'left_field' => 'nid',
      'field' => 'nid',
  );

  // fields

  // dc_id
  $data['tic_carto_count']['dcid'] = array(
    'title' => t('ID'),
    'help' => t('The download count ID of the download.'),
    'field' => array(
      'handler' => 'views_handler_field_numeric',
      'click sortable' => TRUE,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_numeric',
    ),
  );

    // id (entity id)
  $data['tic_carto_count']['nid'] = array(
    'title' => t('Entity ID'),
    'help' => t('The id of the entity from which the file was downloaded.'),
    'field' => array(
      'handler' => 'views_handler_field_numeric',
      'click sortable' => TRUE,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_numeric',
    ),
  );

  $data['tic_carto_count']['count'] = array(
    'title' => t('Download Count'),
    'help' => t('Times the file was downloaded.'),
    'field' => array(
      'handler' => 'views_handler_field_numeric',
      'click sortable' => TRUE,
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_numeric',
    ),
  );

  return $data;
}


function tic_carto_count_views_data_alter(&$data) {
  // join for files table
  $data['file_managed']['table']['join']['tic_carto_count'] = array(
      'left_field' => 'fid',
      'field' => 'fid',
  );
}
