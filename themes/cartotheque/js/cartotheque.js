// Sliders homepage
jQuery(function() {
    jQuery(".rslides").responsiveSlides({
	nav: true,
	namespace: "callbacks",
	});
});


//Advanced search
jQuery(function() {
	jQuery( "#datepicker" ).datepicker();
	jQuery( "#accordion" ).accordion({
		active: 0,
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
	jQuery('#edit-field-mots-cles-tid').chosen();
	jQuery('#edit-field-mots-cles-thesaurus-tid').chosen();
	
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
	var searchBar = jQuery('.region-search');
	var screen = jQuery(window);
	
	if (screen.width() > 768) {
		searchBar.sticky({topSpacing:55});
	}
});

