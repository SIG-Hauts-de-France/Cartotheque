<?php

/**
 * @file
 * Administrative page callbacks for the tic_carto_count module.
 */

/**
 * Download count administration settings.
 *
 * @return
 * Form for tic_carto_count configuration options.
 */
function tic_carto_count_admin_settings($form, &$form_state) {
  $form['excluded file extensions'] = array(
    '#type' => 'fieldset',
    '#title' => t('Excluded file extensions'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );
  $form['excluded file extensions']['tic_carto_count_excluded_file_extensions'] = array(
    '#type' => 'textfield',
    '#title' => t('Excluded file extensions'),
    '#default_value' => variable_get('tic_carto_count_excluded_file_extensions', 'jpg jpeg gif png'),
    '#maxlength' => 255,
    '#description' => t('To exclude files of certain types, enter the extensions to exclude separated by spaces. This is useful if you have private image fields and don\'t wish to include them in download counts.'),
  );
  $form['download count page'] = array(
    '#type' => 'fieldset',
    '#title' => t('Report page options'),
    '#description' => t('Settings for <a href="@page">this</a> page.', array('@page' => url('admin/reports/download-count'))),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['download count page']['tic_carto_count_view_page_title'] = array(
    '#type' => 'textfield',
    '#title' => t('Title'),
    '#default_value' => variable_get('tic_carto_count_view_page_title', t('Carto Download Counts')),
  );
  $form['download count page']['tic_carto_count_view_page_limit'] = array(
    '#type' => 'textfield',
    '#title' => t('Total number of items to display'),
    '#size' => 10,
    '#default_value' => variable_get('tic_carto_count_view_page_limit', 0),
    '#description' => t('Set to 0 for no limit.'),
  );
  $form['download count page']['tic_carto_count_view_page_items'] = array(
    '#type' => 'textfield',
    '#title' => t('Number of items per page'),
    '#size' => 10,
    '#default_value' => variable_get('tic_carto_count_view_page_items', 25),
    '#description' => t('Set to 0 for no pager.'),
  );
  $header = variable_get('tic_carto_count_view_page_header', '');
  $form['download count page']['tic_carto_count_view_page_header'] = array(
    '#type' => 'text_format',
    '#title' => t('Header'),
    '#format' => isset($header['format']) ? $header['format'] : NULL,
    '#default_value' => isset($header['value']) ? $header['value'] : NULL,
    '#description' => t('Text to appear between the title of the page and the download count table.'),
  );
  $footer = variable_get('tic_carto_count_view_page_footer', '');
  $form['download count page']['tic_carto_count_view_page_footer'] = array(
    '#type' => 'text_format',
    '#title' => t('Footer'),
    '#format' => isset($footer['format']) ? $footer['format'] : NULL,
    '#default_value' => isset($footer['value']) ? $footer['value'] : NULL,
    '#description' => t('Text to appear underneath the download count table.'),
  );

  if (module_exists('libraries')) {
    $plugin = libraries_get_path('jquery.sparkline', FALSE) . '/jquery.sparkline.min.js';
  }
  else {
    $plugin = drupal_get_path('module', 'tic_carto_count') . '/jquery.sparkline.min.js';
  }

  if (file_exists($plugin)) {
    $form['details']['sparklines'] = array(
      '#type' => 'fieldset',
      '#title' => t('Sparkline Options'),
      '#description' => '<p>' . t('The jquery sparkline plugin is available at: /') . $plugin . '</p>',
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $form['details']['sparklines']['tic_carto_count_sparklines'] = array(
      '#type' => 'select',
      '#title' => t('Sparklines'),
      '#default_value' => variable_get('tic_carto_count_sparklines', 'line'),
      '#options' => array(
        'none' => t('None'),
        'line' => t('Line'),
        'bar' => t('Bar'),
      ),
      '#disabled' => !$plugin,
      '#description' => 'Enable sparklines and select type.',
    );
    $form['details']['sparklines']['tic_carto_count_sparkline_min'] = array(
      '#type' => 'textfield',
      '#title' => t('Chart Minimum Value'),
      '#size' => 8,
      '#default_value' => variable_get('tic_carto_count_sparkline_min', '0'),
      '#disabled' => !$plugin,
      '#description' => 'Specify the minimum value to use for the range of the chart (starting point).',
    );
    $form['details']['sparklines']['tic_carto_count_sparkline_height'] = array(
      '#type' => 'textfield',
      '#title' => t('Chart Height'),
      '#size' => 8,
      '#default_value' => variable_get('tic_carto_count_sparkline_height', '150px'),
      '#disabled' => !$plugin,
      '#description' => 'The height of the sparkline graph. May be any valid css height (ie 1.5em, 20px, etc). Must include units.',
    );
    $form['details']['sparklines']['tic_carto_count_sparkline_width'] = array(
      '#type' => 'textfield',
      '#title' => t('Chart Width'),
      '#size' => 8,
      '#default_value' => variable_get('tic_carto_count_sparkline_width', '50%'),
      '#disabled' => !$plugin,
      '#description' => 'The width of the sparkline graph. May be any valid css width (ie 1.5em, 20px, etc). Must include units.',
    );
  }

  $form['tic_carto_count_flood_control'] = array(
    '#type' => 'fieldset',
    '#title' => t('Flood Control Settings'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['tic_carto_count_flood_control']['tic_carto_count_flood_limit'] = array(
    '#type' => 'textfield',
    '#title' => t('Flood control limit'),
    '#size' => 10,
    '#default_value' => variable_get('tic_carto_count_flood_limit', 0),
    '#description' => 'Maximum number of times to count the file download per time window. Enter 0 for no flood control limits.',
  );
  $form['tic_carto_count_flood_control']['tic_carto_count_flood_window'] = array(
    '#type' => 'textfield',
    '#title' => t('Flood control window'),
    '#size' => 10,
    '#default_value' => variable_get('tic_carto_count_flood_window', 5),
    '#description' => 'Number of seconds in the time window for counting a file download.',
  );

  return system_settings_form($form);
}

