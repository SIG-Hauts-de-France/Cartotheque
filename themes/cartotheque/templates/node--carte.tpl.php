<?php

/**
 * @file
 * Default theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct URL of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type; for example, "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type; for example, story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $view_mode: View mode; for example, "full", "teaser".
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * Field variables: for each field instance attached to the node a corresponding
 * variable is defined; for example, $node->body becomes $body. When needing to
 * access a field's raw values, developers/themers are strongly encouraged to
 * use these variables. Otherwise they will have to explicitly specify the
 * desired field language; for example, $node->body['en'], thus overriding any
 * language negotiation rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 *
 * @ingroup themeable
 */


$type_carte = strtolower($node->field_type_de_carte['und'][0]['value']);

?>

<?php print render($title_prefix); ?>
<h2<?php print $title_attributes; ?>>
	<a href="<?php print $node_url; ?>"><?php print $title; ?></a>
	<?php if ($collections_count > 0): ?>
	<span class="tree"></span>
	<?php endif; ?>
	<span class="type"><?php print render($content['field_type_de_carte']); ?></span>
</h2>
<?php print render($title_suffix); ?>

<?php if ($display_submitted): ?>
	<div class="submitted"><?php print $submitted; ?></div>
<?php endif; ?>

<div class="col-sm-5">
	<div class="imgMapFiche"><?php print render($content['field_imagette']); ?></div>
	
	<div class="col-xs-8 infoMap">
	<dl>
		<?php
			if ($type_carte == 'statique' && array_key_exists('field_numero_de_carte',$content)) { 
				$content['field_numero_de_carte']['#label_display'] = 'hidden';
				print '<dt>'; print $content['field_numero_de_carte']['#title']; print ':</dt>';
				print '<dd>'; print render($content['field_numero_de_carte']); print '</dd>';
			}
		?>
		
		<?php if($type_carte == 'dynamique'): ?>
		<?php if(array_key_exists('field_date_de_mise_jour',$content)) : ?>
		<?php $content['field_date_de_mise_jour']['#label_display'] = 'hidden'; ?>
		<dt><?php print $content['field_date_de_mise_jour']['#title']; ?> :</dt>
		<dd><?php print render($content['field_date_de_mise_jour']); ?></dd>
		<?php endif; ?>
		<?php endif; ?>
		
		<?php if(array_key_exists('field_date_de_creation',$content)) : ?>
		<?php $content['field_date_de_creation']['#label_display'] = 'hidden'; ?>
		<dt><?php print $content['field_date_de_creation']['#title']; ?> :</dt>
		<dd><?php print render($content['field_date_de_creation']); ?></dd>
		<?php endif; ?>
	</dl>
	</div>
	
	<div class="col-xs-4" style="text-align:right;">
		<div class="nbAction">
			<?php print $stats_total_count ?><span class="nbImg"></span>
			<?php if ($type_carte == 'statique'): ?>
			<?php print $download_count ?><span class="nbPdf"></span>
			<?php endif; ?>
		</div>
	</div>

	<?php
		$infosGenerales = '<h4>Informations</h4>';
		
		$infosGenerales .= "<dl>";
		if(array_key_exists('field_auteur',$content)) {
			$content['field_auteur']['#label_display'] = 'hidden';
			$infosGenerales .= "<dt>".$content['field_auteur']['#title'].":</dt>";
			$infosGenerales .= "<dd>".render($content['field_auteur']['#items'][0]['node']->title)."</dd>";
		}
		if(array_key_exists('field_emprise_geographique',$content)) {
			$content['field_emprise_geographique']['#label_display'] = 'hidden';
			$infosGenerales .= "<dt>".$content['field_emprise_geographique']['#title'].":</dt>";
			$infosGenerales .= "<dd>".render($content['field_emprise_geographique'])."</dd>";
		}
		if($type_carte == 'statique' && array_key_exists('field_echelle',$content)) {
			$content['field_echelle']['#label_display'] = 'hidden';
			$infosGenerales .= "<dt>".$content['field_echelle']['#title'].":</dt>";
			$infosGenerales .= "<dd>".render($content['field_echelle'])."</dd>";
		}
		$infosGenerales .= "</dl>";

		$infosGenerales .= '<dl class="themaMap">';
		$infosGenerales .= '<p><u>Thématiques:</u></p>';		
		if(array_key_exists('field_categorie',$content)) {
			$content['field_categorie']['#label_display'] = 'hidden';
			$infosGenerales .= '<dt>'.$content['field_categorie']['#title'].':</dt>';
			$infosGenerales .= '<dd>'. $categoryName .'</dd>';
		}
		if(array_key_exists('field_thematique',$content)) {
			$content['field_thematique']['#label_display'] = 'hidden';
			$infosGenerales .= '<dt>'.$content['field_thematique']['#title'].':</dt>';
			$infosGenerales .= '<dd>'. $thematiquesLinks .'</dd>';
		}
		$infosGenerales .= '</dl>';
	
		$infosGenerales .= "<dl>";
		$infosGenerales .= '<dt>Sources de données :</dt>';
		$infosGenerales .= '<dd>' .
					render($content['field_date_source_des_donnees']) .
					render($content['field_source_des_donnees']) .
					render($content['field_url_source_des_donnees']) .
					'</dd>';
		$infosGenerales .= '</dl>';
		
	?>

	<div class="hidden-xs col-sm-12 infosGenerales">
		<?php print $infosGenerales; ?>
	</div>
</div>

<div class="col-xs-12 col-sm-7">
	<div class="descMapFiche">
		<?php print render($content['field_description']); ?>
		<div class="linkTheMap">
			<?php if($type_carte == 'dynamique' && $node->field_url_carte['und'][0]['value']): ?>
				<span class="urlMap"><a href="<?php print $node->field_url_carte['und'][0]['value']; ?>" target="_blank"><span class="linkIcone"></span>url</a></span>
			<?php endif; ?>
		
			<?php 
				if ($type_carte == 'statique') {
				
					$htmlimage = render($content['field_image_carte']);
					if( preg_match('/<img .* src=\"(.*)\"/U', $htmlimage, $matches) ) {
						print '<span class="imgMap"><a href="'.$matches[1].'"><span class="linkIcone"></span>img</a></span>&nbsp;';
					}
					elseif( preg_match('/<a(.*) href=\"(.*)\"(.*)>/U', $htmlimage, $matches) ) {
						print '<span class="imgMap"><a '.$matches[1].' href="'.$matches[2].'" '.$matches[3].'><span class="linkIcone"></span>img</a></span>&nbsp;';
					}

					$htmlfile = render($content['field_fichier_carte']);
					if( preg_match('/<a(.*) href=\"(.*)\" (.*)>/U', $htmlfile, $matches) ) {
						print '<span class="pdfMap"><a '.$matches[1].' href="'.$matches[2].'" '.$matches[3].'><span class="linkIcone"></span>pdf</a></span>&nbsp;';
					}
				}
			?>
		</div>
		<div class="keyWordMap">Mots clés Inspire : <span class="keyWord"><?php
			$content['field_mots_cles']['#label_display'] = 'hidden';
			if ($inspireKeywordsLinks != '') {
				print $inspireKeywordsLinks;
			}
			else { print ' <span class="no-keywords">-</span> '; }
			?></span>
		</div>
		<div class="keyWordMap">Mots clés: <span class="keyWord"><?php
			$content['field_mots_cles']['#label_display'] = 'hidden';
			if ($keywordsLinks != '') {
				print $keywordsLinks;
			}
			else { print ' <span class="no-keywords">-</span> '; }
			?></span>
		</div>
	</div>
	<?php if ($type_carte == 'statique'): ?>
	<?php if ($collections_count > 0): ?>
	<div class="collectionsMap">
		<h4>Collection(s) à laquelle(s) appartient la carte</h4>
		<?php
			$content['field_collections']['#label_display'] = 'hidden';
			print render($collections);
		?>
	</div>
	<?php endif; ?>
	<?php endif; ?>
	<div class="ressourcesMap">
		<h4>Ressources associées</h4>
		<?php
			$content['field_ressources_associes']['#label_display'] = 'hidden';
			print render($content['field_ressources_associes']);
		?>
	</div>
</div>

<div class="visible-xs col-xs-12 infosGenerales">
	<?php print $infosGenerales; ?>
</div>

<div class="content"<?php print $content_attributes; ?>>
<?php
	// We hide the comments and links now so that we can render them later.
	hide($content['comments']);
	hide($content['links']);
	hide($content['field_type_de_carte']);
	hide($content['field_uuid']);
	hide($content['field_collections']);
	//print render($content);
?>
</div>


