// Sliders homepage
jQuery(function() {
    jQuery(".rslides").responsiveSlides({
	nav: true,
	namespace: "callbacks",
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
	// Attacher Chosen aux select mots cles recherche
	jQuery('#edit-field-mots-cles-tid').chosen({width: "250px"});
	jQuery('#edit-field-mots-cles-thesaurus-tid').chosen({width: "250px"});
	jQuery('#edit-field-thematique-tid').chosen({width: "250px"});
	
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
	var header = jQuery('header');
	
	if (jQuery(document).height() > 850) {
		var top = jQuery('header').offset().top;
		jQuery(window).scroll(function (event) {
			if (jQuery(this).scrollTop() >= 50) {
				header.addClass('fixed-header');
			}
			else {
				header.removeClass('fixed-header');
			}
		});
	}
	
	// Limiter l'effet a la page de résultat des cartes
	if(jQuery(document).find('.map').length < 1) {
		return;
	}
	
	if (screen.width() > 768) {
		searchBar.sticky({
			topSpacing: 140,
			widthFromWrapper: false,
		});
		
	}
});

