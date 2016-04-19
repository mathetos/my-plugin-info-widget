jQuery(document).ready(function( $ ) {
	"use strict";
		$('input.customize').click(function() {
	    if( $(this).is(':checked')) {
	        $("#wprrw_customize").show();
	    } else {
	        $("#wprrw_customize").hide();
	    }
		});
});
