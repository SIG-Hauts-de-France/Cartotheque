

jQuery(document).ready(function() {
	
	//var mapType = jQuery('#edit-field-type-de-carte-und');
	//var echelleInputDiv = jQuery('.form-item-field-echelle-und');
	var thematiqueSelect = jQuery('#edit-field-thematique-und');
	var addContactLink = jQuery('#addContactLink');
	var addContactWindow = jQuery('#createContact');
	var collectionsSelect = jQuery('#edit-field-collections-und');
	//var numeroDeCarte = jQuery('#edit-field-numero-de-carte');
	//var sourceData = jQuery('#edit-field-source-des-donnees');
	//var urlSourceData = jQuery('#edit-field-url-source-des-donnees');
	//var dateSourceData = jQuery('#edit-field-date-source-des-donnees');
	//var dateMiseAJour = jQuery('#edit-field-date-de-mise-jour');
	//var urlCarte = jQuery('#edit-field-url-carte');
	
	// Attacher chosen sur le select categories
	thematiqueSelect.chosen();
	collectionsSelect.chosen();
	
	addContactLink.click(function(e) {
		e.preventDefault();
		jQuery('#page').append('<div id="createContact" style="display:none" title="Ajouter un contact"><form id="createContactAjax"><label for="nom">Nom: </label><input type="text" name="nom" class="text-full form-text"><label for="email">Email: </label><input type="text" name="email" class="text-full form-text"><br /><input type="submit" class="form-submit" value="Ajouter un contact" id="addContactSubmit"></form></div>');
		jQuery('#addContactSubmit').click(function(e) {
			e.preventDefault();
			var nom = jQuery('#createContactAjax input[name=nom]').val();
			var email = jQuery('#createContactAjax input[name=email]').val();
			console.debug(nom);
			console.debug(email);
			jQuery.ajax({
				url: '?q=tic_geosource/create_contact_ajax',
				type: 'GET',
				data: 'nom='+nom+'&email='+email,
				success: function(data) {
					console.debug(data);
					if(data.code == 'success') {
						jQuery('#edit-field-auteur-und').append('<option value="'+data.nid+'">'+data.title+'</option>');
						jQuery('#createContact').html('<p>Le contact a bien été ajouté !</p>');
						jQuery('#edit-field-auteur-und').val(data.nid);
					}
					else {
						//TODO: ajouter classe error au champ non validé + message erreur dans la dialog box
						alert('Une erreur est survenue');
					}
				},
				error: function(err) {
					alert('Une erreur est survenue...');
				},
			});
		});
		jQuery('#createContact').dialog();
	});
	
});
