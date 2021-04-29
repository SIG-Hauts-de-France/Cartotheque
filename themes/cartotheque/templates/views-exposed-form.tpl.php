<?php

/**
 * @file
 * This template handles the layout of the views exposed filter form.
 *
 * Variables available:
 * - $widgets: An array of exposed form widgets. Each widget contains:
 * - $widget->label: The visible label to print. May be optional.
 * - $widget->operator: The operator for the widget. May be optional.
 * - $widget->widget: The widget itself.
 * - $sort_by: The select box to sort the view using an exposed form.
 * - $sort_order: The select box with the ASC, DESC options to define order. May be optional.
 * - $items_per_page: The select box with the available items per page. May be optional.
 * - $offset: A textfield to define the offset of the view. May be optional.
 * - $reset_button: A button to reset the exposed filter applied. May be optional.
 * - $button: The submit button for the form.
 *
 * @ingroup views_templates
 */
?>
<?php if (!empty($q)): ?>
  <?php
    // This ensures that, if clean URLs are off, the 'q' is added first so that
    // it shows up first in the URL.
    print $q;
  ?>
<?php endif; ?>

<?php if(drupal_is_front_page()): ?>
	<?php foreach ($widgets as $id => $widget): ?>
	<?php if($widget->id == "edit-combine" && false) print $widget->widget; ?>
	<?php if($widget->id == "edit-pgsql-combine-filter-views") print $widget->widget; ?>
	<?php if($widget->id == "edit-search-api-views-fulltext") print $widget->widget; ?>
	<?php endforeach; ?>
<?php else: ?>
<?php
	//On trie les champs
	$combine = array(); $other = array(); $filter = array(); $secondary = array();
	foreach ($widgets as $id => $widget) {
		//if($id=="filter-combine") $combine[] = $widget;
		if($id=="filter-pgsql_combine_filter_views") $combine[] = $widget;
		elseif($id=="filter-search_api_views_fulltext") $combine[] = $widget;
		elseif($id=="sort-sort_bef_combine") $filter = $widget;
		elseif($id=="filter-field_type_de_carte_value" || $id=="filter-field_cartotheque_value") $secondary[] = $widget;
		elseif($id=="filter-field_type_de_carte" || $id=="filter-field_cartotheque") $secondary[] = $widget;
		else $other[] = $widget;
		//echo "<pre>".$id."</pre>";
	}

?>
<div class="views-exposed-form">
  <div class="views-exposed-widgets clearfix">

	<div class="filtreList row">
		<div class="form-group col-sm-7">
		<?php if (!empty($items_per_page)): ?>
			<?php print $items_per_page; ?>
		<?php endif; ?>
		</div>
		<div class="form-group orderList col-sm-5">
		<?php if (!empty($filter)): ?>
			<?php print $filter->widget; ?>
		<?php endif; ?>
		</div>
	</div>


	<?php foreach($combine as $id => $widget): ?>
		<?php print $widget->widget; ?>
	<?php endforeach; ?>

<div id="accordion" style="clear:left;">
<h3>Recherche avancée</h3>
<div>
<?php foreach($other as $id => $widget): ?>
      <div id="<?php print $widget->id; ?>-wrapper" class="views-exposed-widget views-widget-<?php print $id; ?>" style="clear:left;">
        <?php if (!empty($widget->label)): ?>
          <label for="<?php print $widget->id; ?>">
            <?php print $widget->label; ?>
          </label>
        <?php endif; ?>
        <?php if (!empty($widget->operator)): ?>
          <div class="views-operator">
            <?php print $widget->operator; ?>
          </div>
        <?php endif; ?>
        <div class="views-widget">
          <?php print $widget->widget; ?>
        </div>
        <?php if (!empty($widget->description)): ?>
          <div class="description">
            <?php print $widget->description; ?>
          </div>
        <?php endif; ?>
      </div>
<?php endforeach; ?>
</div>
<h3>Critères</h3>
<div>
<?php foreach($secondary as $id => $widget): ?>
      <div id="<?php print $widget->id; ?>-wrapper" class="views-exposed-widget views-widget-<?php print $id; ?>" style="clear:left;">
        <?php if (!empty($widget->label)): ?>
          <label for="<?php print $widget->id; ?>">
            - <?php print $widget->label; ?>
          </label>
        <?php endif; ?>
        <?php if (!empty($widget->operator)): ?>
          <div class="views-operator">
            <?php print $widget->operator; ?>
          </div>
        <?php endif; ?>
        <div class="views-widget">
          <?php print $widget->widget; ?>
        </div>
        <?php if (!empty($widget->description)): ?>
          <div class="description">
            <?php print $widget->description; ?>
          </div>
        <?php endif; ?>
      </div>
<?php endforeach; ?>
</div>
</div>

    <?php if (!empty($sort_by)): ?>
      <div class="views-exposed-widget views-widget-sort-by">
        <?php print $sort_by; ?>
      </div>
      <div class="views-exposed-widget views-widget-sort-order">
        <?php print $sort_order; ?>
      </div>
    <?php endif; ?>


    <?php if (!empty($offset)): ?>
      <div class="views-exposed-widget views-widget-offset">
        <?php print $offset; ?>
      </div>
    <?php endif; ?>

    <div class="views-exposed-widget views-submit-button">
      <?php print $button; ?>
    </div>

     <?php if (!empty($reset_button)): ?>
      <div class="views-exposed-widget views-reset-button">
        <?php print $reset_button; ?>
      </div>
    <?php endif; ?>

  </div>
</div>
<?php endif; ?>
