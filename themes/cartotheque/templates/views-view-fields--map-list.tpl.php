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

<div class="map">
	<h2><?php print $fields['title']->content ?>
	<?php if (strtolower($fields['field_type_de_carte']->content) == 'statique'): ?>
	<?php if (strlen($fields['field_collections']->content) > 50): ?>
		<div class="abulle">
		<span class="tree"></span>
		<span class="infobulle">Cette carte fait partie des collections suivantes:<br>
			<ul>
			<?php
				foreach ($row->field_field_collections as $col) {
					print '<li><a href="'.theme_get_setting('cartotheque_map_list_url') . '&field_collections_tid=' .$col['rendered']['#options']['entity']->tid.'">'.$col['rendered']['#title'].'</a></li>';
				}
			?>
			</ul>
		</span>
		</div>
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
					<a href="<?php print $fields['field_image_carte']->content; ?>&countdl=yes" download> </a>
				</span>
				<?php endif; ?>
				<?php if ($fields['field_fichier_carte']->content): ?>
				<span class="linkPdf">
					<a href="<?php print $fields['field_fichier_carte']->content; ?>&countdl=yes" download> </a>
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
			
			<?php
			if ($fields['field_fichier_carte']->content) {
				print '<div class="file-dialog" style="display: none;" title="Fichier carte">';
				print '<span class="ui-helper-hidden-accessible"><input type="text" /></span>';
				print '<span class="linkShow"><a href="'.$fields['field_fichier_carte']->content.'" target="_blank">Visualiser</a></span>';
				print '<span class="linkDl"><a href="'.$fields['field_fichier_carte']->content.'" download>Télécharger</a></span>';
				print '</div>';
			}
			
			if ($fields['field_image_carte']->content) {
				print '<div class="image-dialog" style="display: none;" title="Image carte">';
				print '<span class="ui-helper-hidden-accessible"><input type="text" /></span>';
				print '<span class="linkShow"><a href="'.$fields['field_image_carte']->content.'" target="_blank">Visualiser</a></span>';
				print '<span class="linkDl"><a href="'.$fields['field_image_carte']->content.'" download>Télécharger</a></span>';
				print '</div>';
			}
			
			?>
		</div>
	</div>
	<div class="nbAction">
		<div class="anb">
		<?php print $fields['totalcount']->content; ?>
		<span class="nbImg"> </span>
		<span class="infobulleNb">
			<?php print $fields['totalcount']->content .' vue(s) pour cette carte'; ?>
		</span>
		</div>
		<?php if (strtolower($fields['field_type_de_carte']->content) == 'statique'): ?>
		<div class="anb">
		<?php if(array_key_exists('count', $fields)): ?>
		<?php print $fields['count']->content; ?>
		<?php else: ?>
		0
		<?php endif; ?>
		<span class="nbPdf"> </span>
		<span class="infobulleNb">
			<?php
				if (array_key_exists('count', $fields)) {
					print $fields['count']->content . ' téléchargement(s) pour cette carte';
				}
				else {
					print 'Aucun téléchargement pour cette carte';
				}
			?>
		</div>
		<?php endif; ?>
	</div>
	<div class="mapDate">
		<?php if ($fields['field_date_de_creation']->content): ?>
		<?php print $fields['field_date_de_creation']->content; ?>
		<?php endif; ?>
	</div>
</div>
<div id="dialog-window" style="display: none;" title="Visualiser ou télécharger"></div>
