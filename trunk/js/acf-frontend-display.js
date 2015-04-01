/* add validation */
jQuery(document).ready(function( $ ) {
	$( '.acf_postbox .field, .form-group' ).each(function( index ) {
		if(  $( this ).hasClass('required') == true ){
			//$( this ).css('border','1px solid red');
			$( this ).find('input').prop('required',true);

			/* PL LOCALE */
			// $( this ).find('input').attr('oninvalid','this.setCustomValidity("To pole nie może być puste")');
		}
	});
});