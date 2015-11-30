<?php

/* Paramètres du chemin vers la liste de cartes */
function cartotheque_form_system_theme_settings_alter(&$form, $form_state) {
  $form['cartotheque_map_list_url'] = array(
    '#type'          => 'textfield',
    '#title'         => t('Url vers la liste des cartes'),
    '#default_value' => theme_get_setting('cartotheque_map_list_url'),
    '#description'   => t("Define map list page url."),
  );
}

