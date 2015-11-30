jQuery(document).ready(function() {
	
	var mapType = jQuery('#edit-field-type-de-carte-und');
	var echelleInputDiv = jQuery('.form-item-field-echelle-und');
	var numeroDeCarte = jQuery('#edit-field-numero-de-carte');
	var sourceData = jQuery('#edit-field-source-des-donnees');
	var urlSourceData = jQuery('#edit-field-url-source-des-donnees');
	var dateSourceData = jQuery('#edit-field-date-source-des-donnees');
	var dateMiseAJour = jQuery('#edit-field-date-de-mise-jour');
	var urlCarte = jQuery('#edit-field-url-carte');
	var fichierCarte = jQuery('#edit-field-fichier-carte');
	
	function cartoDynamiqueForm() {
		echelleInputDiv.slideUp();
		numeroDeCarte.slideUp();
		sourceData.slideUp();
		urlSourceData.slideUp();
		dateSourceData.slideUp();
		fichierCarte.slideUp();
		
		dateMiseAJour.slideDown();
		urlCarte.slideDown();
	}
	
	function cartoStatiqueForm() {
		echelleInputDiv.slideDown();
		numeroDeCarte.slideDown();
		sourceData.slideDown();
		urlSourceData.slideDown();
		dateSourceData.slideDown();
		fichierCarte.slideDown();
		
		dateMiseAJour.slideUp();
		urlCarte.slideUp();
	}
	
	var currentMapType = mapType.val();
	
	if (mapType.val() == 'Dynamique') {
		cartoDynamiqueForm();
	}
	else {
		cartoStatiqueForm();
	}
	
	mapType.change(function() {
		if (mapType.val() == 'Statique') {
			cartoStatiqueForm();
		}
		else {
			cartoDynamiqueForm();
		}
	});
});
