<?php
/**
 * Available variables:
 *
 * $search_form
 * $search
 * $form
 *
 *
 */

?>

<div class="input-group">
	<span class="input-group-addon search" id="basic-addon-search"></span>
	<input type="text" class="form-control" id="edit-search-block-form--2" name="search_block_form" placeholder="<?php print t('Search') ?>" aria-describedby="basic-addon-search">
</div>
<?php
	echo $search['hidden'];
	echo $search['type_de_carte'];
	echo $search['actions'];
	//var_dump($search); die();
?>
