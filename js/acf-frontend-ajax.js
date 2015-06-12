window.adf_forms_queue = 0;
window.loaded_scripts = [];
var AFD_render_form;
var preprocess_ajax_form;
var AFD_get_ajax_form;
var required_guardian = false; // CHECK IS FORM VAVE REQURED FIELDS


jQuery(document).ready(function( $ ){ 



	function form_to_json (selector) {
		var ary = $(selector).serializeArray();
		var obj = {};
		for (var a = 0; a < ary.length; a++) obj[ary[a].name] = ary[a].value;
		return obj;
	}
	//$( document ).on('click',"body#acf_ajax_submit",function() {
	$( '#acf_ajax_submit' ).live('click',function() {

		formID = $(this).closest('form').attr('id');
		
		if(required_guardian == false){
			//alert('ok - no required fields');
		}else{
			//alert('forms have required fields - check it');
			// /alert(validate_ajax_form(formID) );
			if( validate_ajax_form(formID) != true ){

				//alert('form isnt valid');
				return false
			}
		}


		var callback = $(this).attr('data-callback');
		jQuery.ajax({
			type: "POST",
			url: window.pluginURI+'ajax/process_ajax_form.php',
			data: form_to_json ('#'+formID)
		})
		.done(function( response ) {
			// CALLBACK FUNCTION
			if (callback in window) {
				window[callback](response);
			}
		});
	});




	AFD_render_form = function(args){
	

			var global_args = args;
			get_form_from_array(global_args);
			
			function get_form_from_array(global_args){

				

				if(global_args[window.adf_forms_queue] == undefined){
					window.adf_forms_queue = 0;

					return false;

				}

				
				args = global_args[window.adf_forms_queue];
				
				window.adf_forms_queue++;



				//AFD_load_scripts(args['scripts']);

				args = preprocess_ajax_form(args,args['args']);
				delete args.args;

				$(args.render_by_id).append('<div class="afd_loader" style="padding:5px"> ŁADOWANIE DANYCH...</div>');

				jQuery.post(window.pluginURI+'inc/afd_acf_extend_api.php' , args, function(response) {
					
					/* RENDER FORM */
					$('.afd_loader').remove();
					$(args.render_by_id).append(response);

					/* GOOGLE MAP AJAX HACK */
					if(args['google_map'] == true){
						if(jQuery('#form-container-'+args['post_id']).find('.acf-google-map').length != 0){
							acf.fields.google_map.set({ $el : jQuery('#form-container-'+args['post_id']).find('.acf-google-map') }).init();
						}
					}
					get_form_from_array(global_args);

				});
		
			}

		
	}



	preprocess_ajax_form = function(args,response){
		ADF_display_props = response;
		$.each(ADF_display_props, function( index, value ) {
			args[index] = value;
		});
		return args;
	}

	/* chesk is form has required fields */
	required_check = function(form_id){
		$('#'+form_id+' .field').each(function( index ) {
			if( $( this ).hasClass('required') ){
				$( this ).find('input').prop('required',true);
		  		required_guardian = true;
		  	}
		});
	}

	validate_ajax_form = function(form_id){

		
if($('#acf-field-liczba_uczestnikow-ograniczona').prop("checked") == false){
	$('#acf-field-liczba_uczestnikow-dowolna').prop("checked", true);
	$('#acf-field-liczba_ograniczona_do').val('');
	
}else{
	if( $('#acf-field-liczba_ograniczona_do').val() == ''){
		$('#acf-field-liczba_ograniczona_do').val('21');
	}
}

if($('#acf-field-dostepnosc-tylko_na_zaproszenie').prop("checked") == false){
	$('#acf-field-dostepnosc-kazdy_moze_dolaczyc').prop("checked", true);
}



		var VALIDATE_guardian = true;


		
		$('#'+form_id+' input').each(function( index ) {
			
			var field_type = $( this ).closest('.field').attr('data-field_type');
			
			if( $( this ).prop('required')){
				
				if($( this ).val() == ''){
					
					VALIDATE_guardian = false;

					if(field_type == 'google_map'){

						if( $(this).hasClass('search') ){
							VALIDATE_guardian = true;
						}
					}

					$(this).css('border','2px solid red');
				
				}else{
					$(this).css('border','0');
				}

			}
		});

		if($('#'+form_id+' select option:selected' ).val()==''){
			VALIDATE_guardian = false;
			$('#'+form_id+' select').css('border','2px solid red');
				
		}else{

			$('#'+form_id+' select').css('border','0');
		}

		
		
		return VALIDATE_guardian;
	}





});



/* Example AFD callback AJAX FORM function */
afd_callback = function (response){
	
	alert('AFD FORM AJAX RESPONCE\nForm more info check your javascript console');
	console.log( '%c ------------------------------------------- ' , 'color: #1777B7' );
	console.log('Responce from ADF EXAMPLE callback function');
	response = JSON.parse(response);
	console.log(response);
	console.log( '%c ------------------------------------------- ' , 'color: #1777B7' );
}

afd_callback_alert = function (response){
	var message = '<div style="position:fixed; top:0; left:0; z-index:10000; background:#fff; border:1px solid #333; padding:20px;"><pre>';
	message += response;
	message = message.replace(/","/g, '",<br/>"');
	message = message.replace(/}/g, '}<br/>');
	
	message += '</pre></div>';
	jQuery('body').append(message);
}