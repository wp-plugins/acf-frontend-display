<?php

class acf_uigen_uploader extends acf_field
{

	// vars
	var $settings, // will hold info such as dir / path
		$defaults; // will hold default field options
		
	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function __construct()
	{
		// vars
		$this->name = 'uigen_uploader';
		$this->label = __("FronEnd Uploader",'uigen_uploader');
		$this->category = __("Front End",'acf');
		$this->defaults = array(
			'default_value'	=>	'',
			'maxlength'		=>	'',
			'placeholder'	=>	'',
			'prepend'		=>	'',
			'append'		=>	''
		);
		
		
		// do not delete!
    	parent::__construct();
		
		// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.0.0'
		);
		
		load_textdomain( 'uigen_uploader', trailingslashit(dirname(__File__)) . 'lang/' . 'uigen_uploader' . '-' . get_locale() . '.mo' );
	}

	
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function create_field( $field )
	{
		$plugin_url_uploader = 	plugins_url().'/ACF_frontend_display/js/blueimp-jQuery-File-Upload-d45deb1/';
				
		wp_register_script( 'jquery-ui-widget', $plugin_url_uploader.'js/vendor/jquery.ui.widget.js');
		wp_enqueue_script( 'jquery-ui-widget' );

		wp_register_script( 'jquery-iframe-transport',  $plugin_url_uploader.'js/jquery.iframe-transport.js');
		wp_enqueue_script( 'jquery-iframe-transport' );	

		wp_register_script( 'jquery-file-upload',  $plugin_url_uploader.'js/jquery.fileupload.js');
		wp_enqueue_script( 'jquery-file-upload' );
	?><style>
		#progress .bar {background-color:#ddd; padding:3px;}
	</style>
	<script>
		jQuery(document).ready(function($) {
			$(".perventDef").click(function(event){
			  event.preventDefault();
			});
			
			$('#fileupload').fileupload({				
				dataType: 'json',
				done: function (e, data) {	
				alert('done');	
					$.each(data.result, function (index, file) {
						$('<p/>').text(file.name).appendTo(document.body);
					});
				},
				progressall: function (e, data) {	
					alert('progress');	
					var progress = parseInt(data.loaded / data.total * 100, 10);
					$('#progress .bar').css(
						'width',
						progress + '%'
					);
					$('#progress .bar').html(progress);
				},
				done: function (e, data) {	
					alert('done');	
					$('#progress .bar').css(
						'width','100%'
					);
					
					//alert(data.result[0].name);

					
					
					$('#progress .bar').html('upload finished');
					
					<?php
						// display uploaded image
						$upload_dir = wp_upload_dir(); 
						$prew_url =  $upload_dir['baseurl'].'/uigen_'.date("Y").'/thumbnail/';
						$full_url =  $upload_dir['baseurl'].'/uigen_'.date("Y").'/';
						
					?>
					var js_imp_path = "<?php echo $full_url?>"+data.result[0].name;
					$(e['target']).parent().next().children('input').val(js_imp_path);

					$('#dropzone').css('background-image', 'url("'+js_imp_path+'")'); 
					$('#thumbnail_message').text('<?php _e( "To change photo upload it again", "myplugin_textdomain" );?>');
					$('#file_list div').text('File '+ data.result[0].name +' is loaded')

					//alert(js_imp_path);
						
					//$('#acf-field-uploader').val(js_imp_path);
					
					//djqsCodeInjector({lang:'<?php echo ICL_LANGUAGE_CODE?>',post_id:$('#save_post').attr('value'),filename:data.result[0].name},'<?php echo plugins_url().'/uigen/modules/postsManager/ci_add_att_img.php'?>',$('#dropzone'),'preppend'); 
				},
				dropZone: $('#dropzone')
			});					
			
			jQuery(document).bind('dragover', function (e) {
				var dropZone = $('#dropzone'),
					timeout = window.dropZoneTimeout;
				if (!timeout) {
					dropZone.addClass('in');
				} else {
					clearTimeout(timeout);
				}
				if (e.target === dropZone[0]) {
					dropZone.addClass('hover');
				} else {
					dropZone.removeClass('hover');
				}
			/*	window.dropZoneTimeout = setTimeout(function () {						
					window.dropZoneTimeout = null;
					dropZone.removeClass('in hover');
				}, 100);*/
			});
			
			jQuery(document).bind('drop dragover', function (e) {
				e.preventDefault();
			});

			/* reload image  in back history */
			var imgExist = jQuery('.thumbnail_url').text();
			if(imgExist != ''){
				$('#dropzone').css('background-image', 'url("'+imgExist+'")'); 
			}


		});
	</script>
	<div class="acf-taxonomy-field">
	<div class="categorychecklist-holder" style="padding:10px">
	
		<?php

		if(!empty($field['mask']))
			$field['data-mask'] .= $field['mask'];
		if(!empty($field['mask_type']) && $field['mask_type'] != 'none')
			$field['data-type'] .= $field['mask_type'];
			
		// vars
		$o = array( 'id', 'class', 'data-mask', 'data-type', 'name', 'value', 'placeholder' );
		$e = '';
		
		
		// maxlength
		if( $field['maxlength'] !== "" )
		{
			$o[] = 'maxlength';
		}
		
		
		// prepend
		if( $field['prepend'] !== "" )
		{
			$field['class'] .= ' acf-is-prepended';
			$e .= '<div class="acf-input-prepend" >' . $field['prepend'] . '</div>';
		}
		
		
		// append
		if( $field['append'] !== "" )
		{
			$field['class'] .= ' acf-is-appended';
			$e .= '<div class="acf-input-append" >' . $field['append'] . '</div>';
		}


		if ( is_admin() ) {
		     $field_type = 'text';
		} else {
		     $field_type = 'hidden';
		}
		
		
		$e .= '<div class="acf-input-wrap" >';
		$e .= '<input type="' . $field_type . '"';
		
		foreach( $o as $k )
		{
			$e .= ' ' . $k . '="' . esc_attr( $field[ $k ] ) . '"';	

			
			
			if( $k == 'value'){
				echo '<div class="uploader_message alert alert-success">';
				_e("You have added file: ",'uigen_uploader');
			
				$f_path = esc_attr( $field[ $k ]);
				$f_file = basename($f_path); 	
				echo $f_file;		
				
				echo '</div>';

				if(@!is_array(getimagesize(esc_attr( $field[ $k ])))){
				    echo '<div class="uploader_img"><img class="img-thumbnail" width="100%" height="auto" src="'.esc_attr( $field[ $k ] ).'" /></div>';
					?>
					<button class=".perventDef btn btn-danger delete" data-type="DELETE" data-url="<?php echo esc_attr( $field[ $k ] );?>?delete=true">
	                    <i class="glyphicon glyphicon-trash"></i>
	                    <span>Delete</span>
	                </button>
	                <?php
				} 
				else {
				    //echo 'faldse';
				}
				
			}
		}
		
		if($f_file != ''){
			$f_button_add = __("Reload files...",'uigen_uploader');	

			


		}else{
			$f_button_add = __("Add files...",'uigen_uploader');
		}


		$e .= ' />';
		$e .= '</div>';
		
		

 ?>
		<!-- UPLOADER -->

		<div class="uploader_buttons">
						
			<span id="fileUploadButt" onclick ="javascript:document.getElementById('fileupload').click();">
            	<i class="glyphicon glyphicon-plus"></i>
            	<span><input class="uploader_add_files perventDef" type="submit" value="<?php echo $f_button_add; ?>" /></span>
	        </span>

	        <span><input class="perventDef" type="submit" value="<?php echo _e("Remove",'uigen_uploader') ?>" /></span>

			<input  
				id="fileupload" 
				type="file" 
				style='visibility: hidden; width:5px;' 
				name="files[]" 
				data-url="<?php echo $plugin_url_uploader; ?>server/php/?type=<?php echo $fileSubfolder;?>" multiple>

			<div id="file_list"><div class="uploader_message alert alert-info"><?php _e("Uploaded file list is empty.",'uigen_uploader'); ?></div></div>
			
			<div id="progress"  class="progress col-md-12">
				<div class="bar" style="width: 0%;"></div>
			</div> 

		</div>
		<?php
		// return
		echo $e;
	?>
	</div>
	</div>
	<?php	

	}
	
	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add css + javascript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_enqueue_scripts()
	{
		// Note: This function can be removed if not used
		
/*		wp_enqueue_script( 'jquery.meiomask', $this->settings['dir'] . '/js/jquery.meiomask.js', array( 'jquery', 'acf-input' ), $this->settings['version'] );
		wp_enqueue_script( 'meiomask.apply', $this->settings['dir'] . '/js/meiomask.apply.js', array( 'jquery', 'acf-input' ), $this->settings['version'] );*/

	}
	
	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @param	$field	- an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function create_options( $field )
	{
	


		// vars
		$key = $field['name'];


		
		?>

<!-- ############################################################ -->
<!-- ############################################################ -->

<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Upload file",'uigen_uploader'); ?></label>
		<p><?php _e("File Upload widget with multiple file selection, drag&drop support, progress bars, validation and preview images, audio and video for jQuery",'uigen_uploader') ?></p>
	</td>
	<td>

		
		<?php 
		do_action('acf/create_field', array(
			'type'	=>	'text',
			'name'	=>	'fields[' .$key.'][uploaded_url]',
			'value'	=>	$field['uploaded_url'],			
		));
		?>
	</td>
</tr>

<!-- ############################################################ -->
<!-- ############################################################ -->

		<?php
		
	}
	
}

new acf_uigen_uploader();

?>