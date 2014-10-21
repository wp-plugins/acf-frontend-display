<?php
/*
Plugin Name: ACF frontend display
Plugin URI: https://github.com/dadmor/ACF_frontend_display
Description: WordPress plugin to display afd form on frontend your site. This Plugin enhancing the Advanced Custom Fields (ACF) 
Author: gdurtan
Author URI: grzegorz.durtan.pl
*/

function afd_alpaca_lib_init() {

	wp_register_script( 'alpaca-js', plugins_url('/js/alpaca-core.min.js', __FILE__) );
	wp_enqueue_script( 'alpaca-js' );

	wp_register_style( 'alpaca-css', plugins_url('/css/alpaca-wpadmin.css', __FILE__) );
	wp_enqueue_style('alpaca-css');

}
add_action('admin_enqueue_scripts', 'afd_alpaca_lib_init');

require_once( plugin_dir_path( __FILE__ ) . '/inc/afd_acf_extend_api.php' );

/* ACF EXTENTION - INIT UPLAOAD FILE */
function afd_upload_field() {
	
	require_once( plugin_dir_path( __FILE__ ) . '/fields-pack/field-upload-file.php');
}
add_action('acf/register_fields', 'afd_upload_field');


/* METABOX start ------------------------------------ */

function afd_frontend_add_meta_box() {
	
	/* build post types array to display ACF frontend metabox */
	$post_types = get_post_types( '', 'names' ); 
	
	foreach ( $post_types as $key => $value) {
		$screens[] = $key;
	}

	foreach ( $screens as $screen ) {

		/* only editors or administrator can display forms */
		if( current_user_can('edit_others_pages') ) {  

			/* display ACF frontend metabox */
			add_meta_box(
				'myplugin_sectionid',
				__( 'afd - view form on front of page', 'myplugin_textdomain' ),
				'afd_frontend_meta_box_callback',
				$screen,
				'side'
			);
 		} 
	}
}
add_action( 'add_meta_boxes', 'afd_frontend_add_meta_box');

function afd_frontend_meta_box_callback( $post ) {

	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'afd_frontend_meta_box', 'afd_frontend_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$value_render = get_post_meta( $post->ID, '_meta_afd_form_render_box_key', true );
	$value_alpaca = get_post_meta( $post->ID, '_meta_afd_form_render_box_alpaca', true );
	
	//echo '<input type="checkbox" id="afd_form_render_box_field" name="afd_form_render_box_field" value="' . esc_attr( $value ) . '" size="25" />';

	if(esc_attr( $value_render ) == 'true'){
		$checked = 'checked=checked';
	}else{
		$checked = '';
	}

	if(afd_form_permision() == true){
		
		echo '<input type="checkbox" id="afd_form_render_box_field" name="afd_form_render_box_field" value="true" size="25" '.$checked.'/>';
		echo '<label for="afd_form_render_box_field">';
		_e( 'check it to display your ACF form', 'myplugin_textdomain' );
		echo '</label> ';

		echo '<input type="hidden" id="afd_alpaca_data" name="afd_alpaca_data" value="'.$value_alpaca.'" size="25" />';

		?>
		<div id="afd_display_more_options" style=""></div> 
		<script type="text/javascript">
			jQuery(document).ready(function($) {
			    $("#afd_display_more_options").alpaca({
			    /* ----------------------------------------------------------------------- */	
			    	<?php if($value_alpaca != ''){ ?>
			    	"data" : <?php echo urldecode ( $value_alpaca );?>,
			    	<? } ?>
			    	"options": {
			    		"fields": {
		                	"dependence_one": {
		                    	"rightLabel": "check it to show extended props"
		               		}
		               	}
			    	},
			    	"schema": {
				      //"title": "Form extended options",
				      //"description": "Define your special display properties",
				      "type": "object",
				      "properties": {

				      	"dependence_one": {
		                    "title": "More form options?",
		                    "type": "boolean"
		                },

				        "id": {
							"type": "string",
							"title": "ID",
							"dependencies": "dependence_one",
							"description": "A unique identifier for the form. Defaults to ‘afd-form’"
				        },
				        "post_id": {
							"type": "string",
							"title": "Post ID",
							"dependencies": "dependence_one",
							"description": "The post ID to load data from and save data to. Defaults to the current post. Can also be set to ‘new_post’ to create a new post on submit",
				        },
/*				        "new_post": {
							"type": "string",
							"title": "New post",
							"dependencies": "dependence_one",
				        },*/
/*				        "field_groups": {
							"type": "string",
							"title": "Field groups",
							"dependencies": "dependence_one",
							"description": "An array of field group IDs to override the field’s which would normally be displayed for the post" 
				        },*/
/*				        "fields": {
							"type": "string",
							"title": "Fiels",
							"dependencies": "dependence_one",
							"description": "An array of field Keys or IDs to override the field’s which would normally be displayed for the post"
				        },*/
/*				        "form_attributes": {
							"type": "string",
							"title": "Form attributes",
							"dependencies": "dependence_one",
							"description": "An array or HTML attributes for the form element."
				        },*/
				        "return": {
							"type": "string",
							"title": "Return",
							"dependencies": "dependence_one",
							"description": "The URL to be redirected to after the post is created / updated. Defaults to the current URL with an extra GET parameter called updated=true. A special placeholder of %post_url% will be converted to post’s permalink (handy if creating a new post)!"
				        },
				        "html_before_fields": {
							"type": "string",
							"dependencies": "dependence_one",
							"title": "Html before fields"
				        },
				        "html_after_fields": {
							"type": "string",
							"dependencies": "dependence_one",
							"title": "Html after fields"
				        },
				        "submit_value": {
							"type": "string",
							"title": "Submit value",
							"dependencies": "dependence_one",
							"description": "A string containing the text displayed on the submit button"
				        },
				        "updated_message": {
							"type": "string",
							"title": "Updated Message",
							"dependencies": "dependence_one",
							"description": "A string message which id displayed above the form after being redirected"
				        },
				        /*"label_placement": {
							"type": "string",
							"title": "Label placement",
							"dependencies": "dependence_one",
							"enum": ['top', 'left']
				        },*/
				        /*"instruction_placement": {
							"type": "string",
							"title": "Instruction placement",
							"dependencies": "dependence_one",
							"description": "Whether to display instructions below the label or field. Defaults to label"
				        },*/
				        "field_el": {
							"type": "string",
							"title": "Field element",
							"dependencies": "dependence_one",
							"enum": ['div', 'tr', 'ul', 'ol', 'dl']
				        },
				       /* "css_type": {
							"type": "string",
							"title": "Field element",
							"dependencies": "dependence_one",
							"enum": ['standard afd', 'bootstrap', 'contactform7']
				        },*/
		        
				      }
				    },
			    /* ----------------------------------------------------------------------- */
				    "postRender": function(renderedForm) {          
		              $('#afd_display_more_options select, #afd_display_more_options input, #afd_display_more_options textarea').live('change',function() {   
		              		
		                //if (renderedForm.isValid(true)) {
		                  var val = renderedForm.getValue();
		                  $('#afd_alpaca_data').val(encodeURIComponent(JSON.stringify(val)));

		                 // $('#<?php echo $my_widget_id."-extra-data"; ?>').val(encodeURIComponent(JSON.stringify(val))); 
		                //}
		              });
		            } 
	            /* ----------------------------------------------------------------------- */
			  });
			});
		</script>
		<?php

		}else{
			echo 'add <a href="'.get_bloginfo('home').'/wp-admin/edit.php?post_type=acf">acf form</a> to this post';
		}

	}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function afd_save_meta_box_data( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['afd_frontend_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['afd_frontend_meta_box_nonce'], 'afd_frontend_meta_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	// Sanitize user input.
	$my_data_afd_render = sanitize_text_field( $_POST['afd_form_render_box_field'] );
	$my_data_afd_alpaca = $_POST['afd_alpaca_data'];

	// Update the meta field in the database.
	update_post_meta( $post_id, '_meta_afd_form_render_box_key', $my_data_afd_render );
	update_post_meta( $post_id, '_meta_afd_form_render_box_alpaca', $my_data_afd_alpaca );
}
add_action( 'save_post', 'afd_save_meta_box_data' );

/* METABOX end ------------------------------------ */


/* DISPLAY filter ------------------------------------ */

function afd_add_form_to_frontend_page($content) {
    
	/* IMPORTANT !!! afd bug fix - disable NOW                       */
	/* This script dont't work in wp_enqueue_script method           */
	/* afd plugin call to it before wp_enqueue finished load         */
	//echo '<script src="'.plugins_url().'/advanced-custom-fields/js/input.js" type="text/javascript" charset="utf-8"></script>';  
	//echo '<script src="'.plugins_url().'/advanced-custom-fields/js/field-group.min.js" type="text/javascript" charset="utf-8"></script>'; 
	/* ------------------------------------------------------------- */
    
    acf_form_head();
    wp_deregister_style( 'wp-admin' );
    global $post;
	$args = json_decode( urldecode ( get_post_meta($post->ID,'_meta_afd_form_render_box_alpaca', true )), true );
	unset($args['dependence_one']);

    echo '<div>'.$content.'<div>';

    /* check display guardian */
    if( get_post_meta( $post->ID, '_meta_afd_form_render_box_key', true) == 'true'){
	   	
	   	echo '<div class="site-main">';

	    	if( empty($args) == true){
			
				/* afd_frontend_form() is afd_form() extended method */
				afd_frontend_form();

			}else{

				/* afd_frontend_form() is afd_form() extended method */
				afd_frontend_form($args);

			}

	    echo '</div>';
	}
}
add_filter( 'the_content', 'afd_add_form_to_frontend_page', 6);