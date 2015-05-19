jQuery(document).ready(function($) {
    $('ul.hl').append('<li  style="float:right"><a href="#" id="more_exports" class="acf-button">Form with page rules</a></li>');
    var ids={};
	$( "#acf-field-acf_posts option" ).each(function( index ) {
	  ids[index] = $( this ).attr('value');
	});
	

$('#more_exports').live('click',function(){

	jQuery.ajax({
			type: "POST",
			url: window.pluginURI+'ajax/acf-export.php',
			data: ids
		})
		.done(function( response ) {
			$( "#acf-field-acf_posts" ).attr('size','15');
			console.log(JSON.parse(response));
			output = '';
			$.each(JSON.parse(response), function( index, value ) {
			  
				output += '<optgroup label="'+value['group_name']+'">'
			  		output += '<option value="'+value['page_value']+'">page: '+value['page_name']+'</option>';
			  		output += '<option value="'+value['form_value']+'">form: '+value['form_name']+'</option>';
			  	output += '</optgroup>'
			});
			$( "#acf-field-acf_posts" ).children().remove();
			$( "#acf-field-acf_posts" ).append(output);

		});

})
	



});

