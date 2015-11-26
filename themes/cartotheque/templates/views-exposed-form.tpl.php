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
	<?php if($widget->id == "edit-combine") print $widget->widget; ?>
	<?php endforeach; ?>
	<p class="searchMore"><span></span><a href="">Recherche avancée</a></p>
<?php else: ?>
<div class="views-exposed-form">
  <div class="views-exposed-widgets clearfix">

	<div class="filtreList row">
		<?php if (!empty($items_per_page)): ?>
		<div class="form-group col-sm-9">
			<?php print $items_per_page; ?>
		</div>
		<?php endif; ?>
		<!--
		<div class="orderList col-sm-3">
			<span class="orderAz"><a href=""></a></span>
			<span class="orderZa"><a href=""></a></span>
			<span class="order01"><a href=""></a></span>
			<span class="order10"><a href=""></a></span>
		</div>
		-->
	</div>

<?php
	//On trie les champs
	$combine = array(); $select = array(); $checkbox = array(); $other = array();
	foreach ($widgets as $id => $widget) {
		if($id=="filter-combine") $combine[] = $widget;
		else $other[] = $widget;
	}

?>

	<?php foreach($combine as $id => $widget): ?>
		<?php print $widget->widget; ?>
	<?php endforeach; ?>

<div id="accordion" style="clear:left;">
<h3>Recherche avancée</h3>
<div>
<?php foreach($other as $id => $widget): ?>
      <div id="<?php print $widget->id; ?>-wrapper" class="views-exposed-widget views-widget-<?php print $id; ?>">
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
