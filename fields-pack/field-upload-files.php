<?php

class acf_uigen_mass_uploader extends acf_field
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
		$this->name = 'uigen_mass_uploader';
		$this->label = __("FronEnd Mass Uploader",'uigen_mass_uploader');
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
		
		load_textdomain( 'uigen_mass_uploader', trailingslashit(dirname(__File__)) . 'lang/' . 'uigen_mass_uploader' . '-' . get_locale() . '.mo' );
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
	
	function create_field( $field ){

		
		$plugin_url_uploader = 	plugins_url( '/js/blueimp-jQuery-File-Upload-d45deb1/', dirname(__FILE__) );
		//echo $plugin_url_uploader.'<br/>';
		
		$upload_dir = wp_upload_dir(); 

		//echo $upload_dir;


		wp_register_script( 'jquery-ui-widget', $plugin_url_uploader.'js/vendor/jquery.ui.widget.js');
		wp_enqueue_script( 'jquery-ui-widget' );

		wp_register_script( 'load-image-all', $plugin_url_uploader.'js/load-image.all.min.js');
		wp_enqueue_script( 'load-image-all' );

		wp_register_script( 'Canvas-to-Blob', $plugin_url_uploader.'js/canvas-to-blob.min.js');
		wp_enqueue_script( 'Canvas-to-Blob' );

		/* uploader 302 status fix (try dont use it) */
		//$plugin_url_uploader = 'https://blueimp.github.io/jQuery-File-Upload';

		wp_register_script( 'jquery-iframe-transport',  $plugin_url_uploader.'js/jquery.iframe-transport.js');
		wp_enqueue_script( 'jquery-iframe-transport' );	

		wp_register_script( 'jquery-file-upload', $plugin_url_uploader.'js/jquery.fileupload.js');
		wp_enqueue_script( 'jquery-file-upload' );

		wp_register_script( 'jquery-file-upload-process',  $plugin_url_uploader.'js/jquery.fileupload-process.js');
		wp_enqueue_script( 'jquery-file-upload-process' );

		wp_register_script( 'jquery-file-upload-image',  $plugin_url_uploader.'js/jquery.fileupload-image.js');
		wp_enqueue_script( 'jquery-file-upload-image' );

		wp_register_script( 'jquery-file-upload-validate',  $plugin_url_uploader.'js/jquery.fileupload-validate.js');

	?>

	
	<div>

	
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
		$field_type = 'hidden';
		if ( is_admin() ) {
		     $field_type = 'text';
		} else {
		     $field_type = 'hidden';
		}

 		//$field_type = 'text';
		
		$e .= '<div class="acf-input-wrap" >';
		$e .= '<input id="mass_uploader_meta" type="' . $field_type . '"';
		
		foreach( $o as $k )
		{
			$e .= ' ' . $k . '="' . esc_attr( $field[ $k ] ) . '"';	

			
			
			if( $k == 'value'){
				
			
				$f_path = esc_attr( $field[ $k ]);
				$f_file = basename($f_path); 	
				
				//echo $f_file;		
				
				echo '</div>';

				
			}
		}		
		if($f_file != ''){
			$f_button_add = __("Reload files...",'uigen_mass_uploader');	
		}else{
			$f_button_add = __("Add files...",'uigen_mass_uploader');
		}
		$e .= ' />';
		$e .= '</div>';
 		?>
		<!-- UPLOADER -->
		<?php
		// return
		echo $e;
?>




<div>

	    <!-- The fileinput-button span is used to style the file input field as button -->
	    <span style="position:relative" class="btn btn-success fileinput-button">
	        Choose file
	        <input style="position:absolute; top:0; left:0; width:100%; line-height:33px; opacity: 0; filter: alpha(opacity=0);" id="fileupload" type="file" name="files[]" multiple>
	    </span>
	    <br>
	    <br>
	    <!-- The global progress bar -->
	    <div id="progress" class="progress">
	        <div class="progress-bar progress-bar-success"></div>
	    </div>
	    <!-- The container for the uploaded files -->
	    <div id="files" class="files"></div>

</div>


<script>
/*jslint unparam: true, regexp: true */
/*global window, $ */
var filesObj = {};
var path = "<?php echo $upload_dir['baseurl'].'/uigen_'.date("Y").'/'; ?>";
console.log('upload patch > '+path);
jQuery(document).ready(function($) {
	$(".perventDef").live('click',function(event){
		event.preventDefault();
	});

	
	//rebuild photo with edit
	var rebuilder = {};
	if( $('#mass_uploader_meta').val() != '' ){
		rebuilder = JSON.parse( decodeURIComponent($('#mass_uploader_meta').val()) );

		$.each(rebuilder, function( index, value ) {
 			var content = '<div class="multi_uploader_row">';
 				content += '<div style="width:100px; float:left; height:100px; background-image:url(\''+value+'\') !important; background-size:cover" ></div>';
				content += '<div style="display:inline">bla</div>';
				content += '<br style="clear:both">';
				content += '<button class="btn btn-warning perventDef float-right" data-url="'+value+'">Delete</button>';
			content += '</div>';
			$('#files').append(content);

		});


	}
	filesObj = rebuilder;
	filesObj = rebuild_files_object();

    'use strict';

    // Change this to the location of your server-side upload handler:
    
    var url = '<?php echo $plugin_url_uploader; ?>server/php/',
        delButton = $('<button/>')
            .addClass('btn btn-warning perventDef float-right')            
            .prop('disabled', true)
            .text('Processing...')
            .on('click', function () {
                /*var $this = $(this),
                    data = $this.data();
                $this
                    .off('click')
                    //.text('button text')
                    .on('click', function () {
                        //$this.remove();
                        //data.abort();
                    });
                data.submit().always(function () {
                    $this.remove();
                });*/
            });
    console.log('server patch > '+url);
    $('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        autoUpload: true,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 1000000, // 5 MB
        // Enable image resizing, except for Android and Opera,
        // which actually support image resizing, but fail to
        // send Blob objects via XHR requests:
        disableImageResize: /Android(?!.*Chrome)|Opera/
            .test(window.navigator.userAgent),
        previewMaxWidth: 100,
        previewMaxHeight: 100,
        previewCrop: true
    }).on('fileuploadadd', function (e, data) {

    	$('input[type=submit]').prop( "disabled", true );

    	$('.progress-bar').css('width','0%');

        data.context = $('<div/>').appendTo('#files');

        $.each(data.files, function (index, file) {
            var node = $('<p class="multi_uploader_row" ></p>')
                    .append($('<span/>').text(file.name));
           
            if (!index) {
                node
                    .append('<br>')
                    .append(delButton.clone(true).data(data));
            }
            node.appendTo(data.context);

        });
    }).on('fileuploadprocessalways', function (e, data) {
        var index = data.index,
            file = data.files[index],
            node = $(data.context.children()[index]);
        if (file.preview) {
            node
                //.prepend('<br>')
                .prepend(file.preview);
        }
        if (file.error) {
            node
                .append('<br>')
                .append($('<span class="text-danger"/>').text(file.error));
        }
        if (index + 1 === data.files.length) {
            data.context.find('button')
                .text('Delete')
                .prop('disabled', !!data.files.error);
        }
    }).on('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress .progress-bar').css(
            'width',
            progress + '%'
        );
    }).on('fileuploaddone', function (e, data) {
    	//alert('done');
    	$('input[type=submit]').prop( "disabled", false );
        $.each(data.result, function (index, value) {
			
			filesObj['image_'+Object.keys(filesObj).length] = path+value.name;
			$('#mass_uploader_meta').val(encodeURIComponent(JSON.stringify(filesObj)));

/*            if (file.url) {

            	filesObj['image_'+Object.keys(filesObj).length] = path+file.name;
				//console.log(filesObj);

				$('#mass_uploader_meta').val(path+file.name);

                var link = $('<a>')
                    .attr('target', '_blank')
                    .prop('href', file.url);
                $(data.context.children()[index])
                    .wrap(link);
            } else if (file.error) {
                var error = $('<span class="text-danger"/>').text(file.error);
                $(data.context.children()[index])
                    .append('<br>')
                    .append(error);
            }*/
        });
    }).on('fileuploadfail', function (e, data) {    	
        $.each(data.files, function (index) {
            var error = $('<span class="text-danger"/>').text('File upload failed.');
            $(data.context.children()[index])
                .append('<br>')
                .append(error);
        });
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');

    /* delete */
    $("#files").find("button").live('click',function(event){
		var container = $(this).parent('.multi_uploader_row');
		var key_to_remove = Object.keys(filesObj)[container.index()]
		delete filesObj[key_to_remove];
		filesObj = rebuild_files_object();
		$('#mass_uploader_meta').val(encodeURIComponent(JSON.stringify(filesObj)));
		container.remove();
	});

	function rebuild_files_object(){
		var techObj = {};
		$.each(filesObj, function( index, value ) {
			techObj['image_'+Object.keys(techObj).length] = filesObj[index]
		});
		filesObj = techObj;
		return filesObj;
	}
});
</script>


	
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
		<label><?php _e("Upload file",'uigen_mass_uploader'); ?></label>
		<p><?php _e("File Upload widget with multiple file selection, drag&drop support, progress bars, validation and preview images, audio and video for jQuery",'uigen_mass_uploader') ?></p>
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

new acf_uigen_mass_uploader();

?>