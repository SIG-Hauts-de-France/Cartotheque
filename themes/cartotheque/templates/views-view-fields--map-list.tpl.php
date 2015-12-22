<?php

/**
 * @file
 * Default simple view template to all the fields as a row.
 *
 * - $view: The view in use.
 * - $fields: an array of $field objects. Each one contains:
 *   - $field->content: The output of the field.
 *   - $field->raw: The raw data for the field, if it exists. This is NOT output safe.
 *   - $field->class: The safe class id to use.
 *   - $field->handler: The Views field handler object controlling this field. Do not use
 *     var_export to dump this object, as it can't handle the recursion.
 *   - $field->inline: Whether or not the field should be inline.
 *   - $field->inline_html: either div or span based on the above flag.
 *   - $field->wrapper_prefix: A complete wrapper containing the inline_html to use.
 *   - $field->wrapper_suffix: The closing tag for the wrapper.
 *   - $field->separator: an optional separator that may appear before a field.
 *   - $field->label: The wrap label text to use.
 *   - $field->label_html: The full HTML of the label to use including
 *     configured element type.
 * - $row: The raw result object from the query, with all data it fetched.
 *
 * @ingroup views_templates
 */
?>

	<?php if (strlen($fields['field_collections']->content) > 50): ?>
	<span class="tree"></span>
	<?php endif; ?>
	<?php endif; ?>
	<span class="type"><?php print $fields['field_type_de_carte']->content ?></span>
	</h2>
	<div class="theMap">
		<div class="imgMap">
			<?php print $fields['field_imagette']->content; ?>
		</div>
		<div class="descMap">
			<?php print $fields['field_description']->content; ?>
			<div class="linkMap">
				<?php if (strtolower($fields['field_type_de_carte']->content) == 'statique'): ?>
				<?php if ($fields['field_image_carte']->content): ?>
				<span class="linkImg">
					<a href="<?php print $fields['field_image_carte']->content; ?>"> </a>
				</span>
				<?php endif; ?>
				<?php if ($fields['field_fichier_carte']->content): ?>
				<span class="linkPdf">
					<a href="<?php print $fields['field_fichier_carte']->content; ?>"> </a>
				</span>
				<?php endif; ?>
				<?php else: ?>
					<?php if ($fields['field_url_carte']->content): ?>
					<span class="linkWeb">
						<a href="<?php print $fields['field_url_carte']->content; ?>" target="_blank"> </a>
					</span>
					<?php endif; ?>
				<?php endif; ?>
				<span class="readMore">
					<?php print $fields['view_node']->content; ?>
				</span>
			</div>
		</div>
	</div>
	<div class="nbAction">
		<?php print $fields['totalcount']->content; ?>
		<span class="nbImg"> </span>
		<?php if (strtolower($fields['field_type_de_carte']->content) == 'statique'): ?>
		<?php if(array_key_exists('count', $fields)): ?>
		<?php print $fields['count']->content; ?>
		<?php else: ?>
		0
		<?php endif; ?>
		<span class="nbPdf"> </span>
		<?php endif; ?>
	</div>
	<div class="mapDate">
		<?php if ($fields['field_date_de_creation']->content): ?>
		<?php print $fields['field_date_de_creation']->content; ?>
		<?php endif; ?>
	</div>
</div>
