/* remove validation */

jQuery(document).ready(function( $ ) {

	$( '.acf_postbox .field' ).each(function( index ) {
		if(  $( this ).hasClass('required') == true ){
			$( this ).removeClass('required');
		}
	});

});