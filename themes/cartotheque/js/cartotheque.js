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
		active: false,
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
