jQuery(document).ready(function( $ ) {
	"use strict";
		$('input.customize').click(function() {
	    if( $(this).is(':checked')) {
	        $("#wprws_customize").show();
	    } else {
	        $("#wprws_customize").hide();
	    }
		});
});
