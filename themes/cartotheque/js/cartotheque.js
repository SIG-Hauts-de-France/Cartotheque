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
		'buttons': { "Close": function() { jQuery(this).dialog('close'); } },
		'close': function(event, ui) { jQuery(this).hide(); }
	};

	jQuery('#imgLink').dialog(dialogOpts);
	jQuery('#pdfLink').dialog(dialogOpts);

	// Fiche carte
	jQuery('.linkTheMap').find('a').click(function(e) {
		if (jQuery(this).parent('span').attr('class') == 'imgMap') {
			jQuery('#imgLink').dialog('open');
			return false;
		}
		else if (jQuery(this).parent('span').attr('class') == 'pdfMap') {
			jQuery('#pdfLink').dialog('open');
			return false;
		}
	});

	//Liste des résultats

	//jQuery('.image-dialog').dialog(dialogOpts);
	//jQuery('.file-dialog').dialog(dialogOpts);
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
jQuery(function() {
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

jQuery(document).ready(function() {
	var chosenOpts = {
		'width': '250px',
		'placeholder_text_multiple': 'Choisir..'
	};
	
	// Attacher Chosen aux select mots cles recherche
	jQuery('#edit-field-mots-cles-tid').chosen(chosenOpts);
	jQuery('#edit-field-mots-cles-thesaurus-tid').chosen(chosenOpts);
	jQuery('#edit-field-thematique-tid').chosen(chosenOpts);
	
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
 * Barre de recherche fixe
 */
jQuery(document).ready(function() {
	var searchBar = jQuery('.sticky-right-bar');
	var screen = jQuery(window);
	var header = jQuery('.fixable-header');
	
	var top = jQuery('header').offset().top;
	
	if (jQuery.browser.mobile) {
		jQuery('body').bind('touchmove', function (event) {
			if (jQuery(this).scrollTop() >= 50) {
				header.addClass('fixed-header');
			}
			else {
				header.removeClass('fixed-header');
			}
		});
	}
	jQuery(window).scroll(function (event) {
		if (jQuery(this).scrollTop() >= 50) {
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


