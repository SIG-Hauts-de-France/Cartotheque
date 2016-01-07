// Sliders homepage
jQuery(function() {
    jQuery(".rslides").responsiveSlides({
	nav: true,
	namespace: "callbacks",
	});
});

// Télécharger ou visualiser
jQuery(document).ready(function() {
	dialogOpts = {
		'autoOpen': false,
		'buttons': { "Fermer": function() { jQuery(this).dialog('close'); } },
		'close': function(event, ui) { jQuery(this).hide(); },
		show: {effect: 'fade', duration: 250},
		hide: {effect: 'fade', duration: 250}
	};

	jQuery('#imgLink').dialog(dialogOpts);
	jQuery('#pdfLink').dialog(dialogOpts);

	// Fiche carte
	jQuery('.linkTheMap').find('a').click(function(e) {
		if (jQuery(this).parent('span').attr('class') == 'imgMap') {
			jQuery('#pdfLink').dialog('close');
			jQuery('#imgLink').dialog('open');
			return false;
		}
		else if (jQuery(this).parent('span').attr('class') == 'pdfMap') {
			jQuery('#imgLink').dialog('close');
			jQuery('#pdfLink').dialog('open');
			return false;
		}
	});

	//Liste des résultats

	jQuery('.linkMap').find('a').click(function (e) {
		if (jQuery(this).parent('span').attr('class') == 'linkImg') {
			var content = jQuery(this).closest('.descMap').find('.image-dialog').html();
			jQuery('#dialog-window').html(content);
			jQuery('#dialog-window').dialog();
			return false;
		}
		else if (jQuery(this).parent('span').attr('class') == 'linkPdf') {
			var content = jQuery(this).closest('.descMap').find('.file-dialog').html();
			jQuery('#dialog-window').html(content);
			jQuery('#dialog-window').dialog();
			return false;
		}
	});
});


function isAdvancedSearch() {
	var sPageURL = decodeURIComponent(window.location.search.substring(1)),
	sURLVariables = sPageURL.split('&'),
	sParameterName,
	i;
	
	// Parameters used for the advanced search
	var advancedSearchParams = [
		'field_emprise_geographique_value',
		'field_mots_cles_tid',
		'field_thematique_tid',
		'field_collections_tid',
		'field_type_de_carte_value'
	];
	
	for (i = 0; i < sURLVariables.length; i++) {
		sParameterName = sURLVariables[i].split('=');
		sParameterName[0] = sParameterName[0].replace(/[\[\]]+/g,'');
		if (advancedSearchParams.indexOf(sParameterName[0]) != -1) {
			if (sParameterName[1] != 'All') {
				return true;
			}
		}
	}
	
	return false;
}

//Advanced search
jQuery( document ).ready( function() {
	jQuery( "#datepicker" ).datepicker();
	
	var accordionActive = false;
	
	if (isAdvancedSearch()) {
		accordionActive = 0;
	}

	if (openAccordion) {
		accordionActive = 0;
	}

	jQuery( "#accordion" ).accordion({
		active: accordionActive,
		collapsible: true
	});


	// Suppression de choix dans le form exposed date
	// Suppression de toutes les options car impossible de les selectionner par jQuery
	jQuery("#edit-field-date-de-creation-value-op option").remove();

	jQuery('#edit-field-date-de-creation-value-op').append('<option value="<">Est inférieur à</option>');
	jQuery('#edit-field-date-de-creation-value-op').append('<option value=">">Est supérieur à</option>');
	jQuery('#edit-field-date-de-creation-value-op').append('<option value="=">Est égal à</option>');
	jQuery('#edit-field-date-de-creation-value-op').append('<option value="contains">Contient</option>');

	jQuery('#edit-field-date-de-creation-value-value-datepicker-popup-1').attr('placeholder', 'Choisir..');

/*
	jQuery( document ).tooltip({
		 position: {
			my: "center bottom-20",
			at: "center top",
			using: function( position, feedback ) {
			  $( this ).css( position );
			  $( "<div>" )
				.addClass( "arrow" )
				.addClass( feedback.vertical )
				.addClass( feedback.horizontal )
				.appendTo( this );
			}
		  }
		});
*/
});

// Attach chosen to advanced search form
jQuery(document).ready(function() {

	//chosen with apple devices is buggy
	if (navigator.userAgent.match(/iPad/i)) { return; }

	var chosenOpts = {
		'width': '250px',
		'placeholder_text_multiple': 'Choisir..'
	};
	
	// Attacher Chosen aux select mots cles recherche
	jQuery('#edit-field-mots-cles-tid').chosen(chosenOpts);
	jQuery('#edit-field-mots-cles-thesaurus-tid').chosen(chosenOpts);
	jQuery('#edit-field-thematique-tid').chosen(chosenOpts);
	jQuery('#edit-field-categorie-tid').chosen(chosenOpts);

});


// 
jQuery( document ).ready(function () {
	
	jQuery('#block-views-newest-maps-block').find('.col-md-6').click(function(e) {
		jQuery('.mapZoom').css('display', 'none');
		jQuery(this).find('.mapZoom').css('display', 'block');
	});
	
	jQuery('#block-views-most-downloaded-block').find('.col-md-6').click(function(e) {
		jQuery('.mapZoom').css('display', 'none');
		jQuery(this).find('.mapZoom').css('display', 'block');
	});
	
	jQuery('.closeZoom').click(function(e) {
		jQuery('.mapZoom').css('display', 'none');
		e.stopPropagation();
	});
	
});

/**
 * Corrections CSS mobile devices
 *
 */
jQuery( document ).ready( function() {
	if (jQuery.browser.mobile) {
		jQuery('header img').css('margin-left', '20px');
		jQuery('header img').css('margin-right', '0px');
		jQuery('header h1').first().remove();
		jQuery('header h1').css('margin-top', '20px');
	}
});

/**
 * Barre de recherche fixe
 */
jQuery(document).ready(function() {
	var searchBar = jQuery('.sticky-right-bar');
	var screen = jQuery(window);
	var header = jQuery('.fixable-header');
	
	var top = jQuery('header').offset().top;

	var triggerTop = 106;

	// touchmove event support	
	if (jQuery.browser.mobile) {
		jQuery('body').bind('touchmove', function (event) {
			if (jQuery(this).scrollTop() >= triggerTop) {
				header.addClass('fixed-header');
			}
			else {
				header.removeClass('fixed-header');
			}
		});
	}

	jQuery(window).scroll(function (event) {
		if (jQuery(this).scrollTop() >= triggerTop) {
			header.addClass('fixed-header');
		}
		else {
			header.removeClass('fixed-header');
		}
	});
	// Limiter l'effet a la page de résultat des cartes
	if(jQuery(document).find('.map').length < 1) {
		return;
	}

	var spaceTop = 140;

	if (jQuery('#mainContent').width() < 980) {

		spaceTop = 115;

		if (jQuery('#admin-menu').length > 0) {
			spaceTop += jQuery('#admin-menu').height();
		}

	}
	
	if (screen.width() > 768) {
		searchBar.sticky({
			topSpacing: spaceTop,
			widthFromWrapper: false,
			touchScroll: jQuery.browser.mobile
		});
		
	}
});


