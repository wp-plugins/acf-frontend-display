/* add validation */
jQuery(document).ready(function( $ ) {
	$( '.acf_postbox .field' ).each(function( index ) {
		if(  $( this ).hasClass('required') == true ){
			//$( this ).css('border','1px solid red');
			$( this ).children('.acf-input-wrap').children('input').prop('required',true);
		}
	});

});