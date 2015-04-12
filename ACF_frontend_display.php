<?php
/*
Plugin Name: ACF frontend display
Plugin URI: https://github.com/dadmor/ACF_frontend_display
Description: WordPress plugin to display afd form on frontend your site. This Plugin enhancing the Advanced Custom Fields (ACF)
Author: gdurtan
Author URI: grzegorz.durtan.pl
Version: 1.3.2
License: GPL2
*/

define( 'ACF_forntend_display' , '1.3.2' );

function afd_admin_lib_init() {

	wp_register_script( 'alpaca-js', plugins_url('/js/alpaca-core.min.js', __FILE__) );
	wp_enqueue_script( 'alpaca-js' );

	wp_register_style( 'alpaca-wpadmin-css', plugins_url('/css/alpaca-wpadmin.css', __FILE__) );
	wp_enqueue_style('alpaca-wpadmin-css');

	/* toggle-switch extra field */
	wp_register_style( 'toggle-switch-css', plugins_url('/css/toggle-switch.css', __FILE__) );
	wp_enqueue_style('toggle-switch-css');

	wp_register_script( 'acf-frontend-display-admin', plugins_url('/js/acf-frontend-display-admin.js', __FILE__) );
	wp_enqueue_script('acf-frontend-display-admin');

}
add_action('admin_enqueue_scripts', 'afd_admin_lib_init');


function afd_fields_frontend_lib_init() {

	if ( !is_admin() ) {

		wp_enqueue_script('jquery');
		wp_register_script( 'acf-frontend-display', plugins_url('/js/acf-frontend-display.js', __FILE__) );
		wp_enqueue_script('acf-frontend-display');

		wp_register_style( 'toggle-switch-css', plugins_url('/css/toggle-switch.css', __FILE__) );
		wp_enqueue_style('toggle-switch-css');

		wp_register_style( 'fields-pack', plugins_url('/css/frontend-fields-pack.css', __FILE__) );
		wp_enqueue_style('fields-pack');

		 wp_register_script( 'acf-frontend-ajax', plugins_url('/js/acf-frontend-ajax', __FILE__) );
        wp_enqueue_script( 'acf-frontend-ajax' );
	}
}
add_action('wp_enqueue_scripts', 'afd_fields_frontend_lib_init');

require_once( plugin_dir_path( __FILE__ ) . '/inc/afd_acf_extend_api.php' );

/* ACF EXTENTION - INIT UPLAOAD FILE */
function afd_upload_field() {

	require_once( plugin_dir_path( __FILE__ ) . '/fields-pack/field-upload-file.php');
	require_once( plugin_dir_path( __FILE__ ) . '/fields-pack/field-upload-files.php');
	require_once( plugin_dir_path( __FILE__ ) . '/fields-pack/field-poolAB-file.php');
	require_once( plugin_dir_path( __FILE__ ) . '/fields-pack/field-hidden-file.php');
	require_once( plugin_dir_path( __FILE__ ) . '/fields-pack/field-date-picker.php');
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
			if( $screen == 'acf' ){
				$title_box = __( 'Display ACF Form', 'acf_frontend_display' );
			}else{
				$title_box = __( 'Display ACF Form', 'acf_frontend_display' );
			}
			/* display ACF frontend metabox */
			add_meta_box(
				'myplugin_sectionid',
				$title_box,
				'afd_frontend_meta_box_callback',
				$screen,
				'side'
			);
 		}
	}
}
add_action( 'add_meta_boxes', 'afd_frontend_add_meta_box');

function afd_frontend_meta_box_callback( $post ) {

	/* create global guardian */
	if( get_post_type( $post->ID ) == 'acf'){

		$gloabal_guardian = false;
			//$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$global_prop = get_post_meta( $post->ID, '_meta_afd_form_global_prop', true );
			
			if($global_prop == 'true'){
				$global_prop = 'checked = "checked"';
			}else{
				$global_prop = '';
			}		
			$rule = get_post_meta($post->ID,'rule',true);
			if( $rule['param'] == 'page'){
				$target_post_id = $rule['value'];
				$target_title = get_the_title($target_post_id);
				$target_link = get_bloginfo('home').'/wp-admin/post.php?post='.$target_post_id.'&action=edit';
			}
			if( $rule['param'] == 'post_type'){
				//$target_post_id = ;
				$target_title = $rule['value'];
				$target_link = get_bloginfo('home').'/wp-admin/edit.php?post_type='.$rule['value'];

			}		
			?>
			<div id="updte_directly" class="alpaca_wpbox_msg">
				<input type="checkbox" id="afd_global_prop" name="afd_global_prop" value="true" size="25" <?php echo $global_prop; ?> />
				Set as global to <b><a href="<?php echo $target_link; ?>"><?php echo $target_title?></a></b>.
			</div>
			<br/>
		<?php
	}
	/* check is globals are defined (in first fieldgroup) */
	/* TODO use new function from API afd_attached_forms_array */
	
	$fieldsArray = afd_form_permision();
	$global_form_id = $fieldsArray[0];
	$global_prop = get_post_meta( $global_form_id, '_meta_afd_form_global_prop', true ); // bolean

	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'afd_frontend_meta_box', 'afd_frontend_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	if( $global_prop == true ){

		echo '<div class="alpaca_wpbox_msg">Set default global properties from ACF form: <a href="'.get_bloginfo('home').'/wp-admin/post.php?post='.$global_form_id.'&action=edit">'.get_the_title($global_form_id).'</a></div></br>';

	}
	$value_render = get_post_meta( $post->ID, '_meta_afd_form_render_box_key', true );
	$value_alpaca = get_post_meta( $post->ID, '_meta_afd_form_render_box_alpaca', true );

	if(esc_attr( $value_render ) == 'true'){
		$checked = 'checked=checked';
	}else{
		$checked = '';
	}	
	if( (afd_form_permision() == true) || ($post->post_type == 'acf') ){

		echo '<div id="afd_render_wraper">';
			
			echo '<input type="checkbox" id="afd_form_render_box_field" name="afd_form_render_box_field" value="true" size="25" '.$checked.'/>';
			echo '<label for="afd_form_render_box_field">';
				_e( '<b>DISPLAY ACF form in CONTENT</b>', 'acf_frontend_display' );
			echo '</label> ';
		
		echo '</div>';
		
		echo '<input type="hidden" id="afd_alpaca_data" name="afd_alpaca_data" value="'.$value_alpaca.'" size="25" />';

		?><div id="afd_display_more_options" style=""></div>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				<?php

				if($global_form_id != NULL){
					echo 'var disabled = true;';

				}else{
					echo 'var disabled = false;';
				}
				?>
			    $("#afd_display_more_options").alpaca({
			    /* ----------------------------------------------------------------------- */
			    	<?php if($value_alpaca != ''){ ?>
			    	"data" : <?php echo urldecode ( $value_alpaca );?>,
			    	<?php } ?>
			    	"options": {

			    		"fields": {
		                	"dependence_one": {
		                    	"rightLabel": "More options"
		               		},
		               		"dependence_ajax": {
		                    	"rightLabel": "AJAX options"
		               		},
		               		"dependence_two": {
		                    	"rightLabel": "Display options"
		               		},
		               		"label_placement": {
		               			"removeDefaultNone": true,
		               		},
		               		"in_content_pos": {
		               			"removeDefaultNone": true,
		               		},
		               		"display_template": {
		                    	"removeDefaultNone": true,
		               		},
		               		"display_login": {
		                    	"rightLabel": "Display for login users",

		               		},
		               		"submit_ajax": {
				        		"rightLabel": "Submit with AJAX",
							
				        	},	
		               		"display_edit": {
		                    	"rightLabel": "Edit by author only",

		               		},

		               		"dependence_three": {
		                    	"rightLabel": "Messages"
		               		},
		               	}
			    	},
			    	"schema": {
				      //"title": "Form extended options",
				      //"description": "Define your special display properties",
				      "type": "object",
				      "properties": {
				      	"dependence_one": {
				      		"disabled" : disabled,
		                    //"title": "More form options?",
		                    "type": "boolean"
		                },		                
				        "form_attributes": {
				        	"disabled" : disabled,
							"type": "string",
							"title": "Form attributes",
							"dependencies": "dependence_one",
							"description": "An array or HTML attributes for the form element."
				        },
				        "html_before_fields": {
				        	"disabled" : disabled,
							"type": "string",
							"dependencies": "dependence_one",
							"title": "Html before fields"
				        },
				        "html_after_fields": {
				        	"disabled" : disabled,
							"type": "string",
							"dependencies": "dependence_one",
							"title": "Html after fields"
				        },
				        "submit_value": {
				        	"disabled" : disabled,
							"type": "string",
							"title": "Submit value",
							"dependencies": "dependence_one",
							"description": "A string containing the text displayed on the submit button"
				        },
				         "updated_message": {
				        	"disabled" : disabled,
							"type": "string",
							"title": "Updated Message",
							"dependencies": "dependence_one",							
							"description": "A string message which id displayed above the form after being redirected"
				        },
				        // --------------------------------------------------------------------
				        "dependence_ajax": {
				      		"disabled" : disabled,
		                    //"title": "More form options?",
		                    "type": "boolean"
		                },
				        "submit_ajax": {
				        	"disabled" : disabled,
							"type": "boolean",
							"title": " ",
							"dependencies": "dependence_ajax",
							"description": "Asynchronous JavaScript and XML"
				        },				        
				        "ajax_callback": {
				        	"disabled" : disabled,
							"type": "string",
							"title": "callback AJAX function name",
							"dependencies": "dependence_ajax",
							"description": "Asynchronous JavaScript and XML",
							"default": "FA_name(callback)",
				        },
				        "render_by_id": {
				        	"disabled" : disabled,
							"type": "string",
							"title": "render_by_id",
							"dependencies": "dependence_ajax",
							//"description": "Asynchronous JavaScript and XML",
							//"default": "FA_name(callback)",
				        },


				       
				        "label_placement": {
				        	"disabled" : disabled,
							"type": "string",
							"title": "Label placement",
							"dependencies": "dependence_one",
							"enum": ['top', 'left' ,'none']
				        },				       
				        "dependence_two": {
				        	"disabled" : disabled,
		                    //"title": "More form options?",
		                    "type": "boolean"
		                },
		                "in_content_pos": {
				        	"disabled" : disabled,
							"type": "string",
							"title": "Position in content",
							"dependencies": "dependence_two",
							"enum": ['disable content','before', 'after']
				        },
				        "display_template": {
				        	"disabled" : disabled,
							"type": "string",
							"title": "Form decoration",
							"dependencies": "dependence_two",
							"enum": ['standard ACF', 'Bootstrap']
				        },
				        
				        "display_login": {
				        	"disabled" : disabled,
							"description": "Display form only for login users",
							"type": "boolean"
				        },
				        "display_edit": {
				        	"disabled" : disabled,
							"description": "Edited mode activated by get &edit=form &guid=user_id",
							"type": "boolean"
				        },
				        "dependence_three": {
				        	"disabled" : disabled,
		                    //"title": "More form options?",
		                    "type": "boolean",
		                    "description": "To support this options install 'Forms actions' plugin",
							
		                },
		                "display_messages_login": {
				        	"disabled" : disabled,
							"type": "string",
							"title": "Must login msg",
							"default": "Login to edit this post.",
							"dependencies": "dependence_three",							
				        },
				        "display_messages_author": {
				        	"disabled" : disabled,
							"type": "string",
							"title": "Must login as author msg.",
							"default": "Login as author to edit this post.",
							"dependencies": "dependence_three",							
				        },
				        "display_messages_after_signon_v_email": {
				        	"disabled" : disabled,
							"type": "string",
							"title": "Signon with email autentication msg.",
							"default": "Mail with activation link was send to: {user_email}",
							"dependencies": "dependence_three",							
				        },
				        "display_messages_after_signon_mail_title": {
				        	"disabled" : disabled,
							"type": "string",
							"title": "Signon email title",
							"default": "{blog_name} : Confirmation email",
							"dependencies": "dependence_three",							
				        },
				        "display_messages_after_signon_mail_content": {
				        	"disabled" : disabled,
							"type": "string",
							"title": "Signon email content",
							"default": "Welcome {user_login} <br/><br/> Please confirm this email by link: {active_link} <br><br>Best<br>{blog_name}",
							"dependencies": "dependence_three",							
				        },
				        "display_messages_hash_true": {
				        	"disabled" : disabled,
							"type": "string",
							"title": "Signon autentication true",
							"default": "Registration completed.",
							"dependencies": "dependence_three",							
				        },
				        "display_messages_hash_false": {
				        	"disabled" : disabled,
							"type": "string",
							"title": "Signon autentication false",
							"default": "Your activation link was expired.",
							"dependencies": "dependence_three",							
				        },
				        "user_not_confirmed": {
				        	"disabled" : disabled,
							"type": "string",
							"title": "User not autenticated",
							"default": "Not auticated user try edit form.",
							"dependencies": "dependence_three",	
				        }
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

						// Clone propreties
						$('.clone_element').on( "click", function () {
							$("#afd_display_more_options input:checkbox").prop('checked', false);
							$.post("<?php echo plugins_url('ajax/frontend-display-alpaca-api.php', __FILE__) ?>" , {'ID':$(this).attr('data-id')}, function(response) {
								console.log(decodeURIComponent(response));
								renderedForm.setValue(JSON.parse(decodeURIComponent(response)));
							});
						});
		            }
	            /* ----------------------------------------------------------------------- */
			  });
			});
		</script>

		<div id="clone_wrapper">
			<div styme="margin:5px 0px">
			<span style="font-size:13px">Clone global properties from:</span><span style="float:right">▼</span>
			</div>
			<ul style="margin:0; font-size:11px">
				<?php
					$anotherForms = apply_filters('acf/get_field_groups', array());
					foreach ($anotherForms as $key => $value) {
						if(get_post_meta($value['id'],'_meta_afd_form_global_prop',true) == true){
							echo '<li class="clone_element" style="margin:0; color:#2ea2cc; text-decoration:underline; cursor:pointer" data-id="'.$value['id'].'">'.get_the_title($value['id']).'</li>';
						}
					}
				?>
			</ul>
		</div>
		<?php

		if($post->post_type == 'acf'){
			?>
		<script>
		
		if(jQuery('#afd_global_prop').attr('checked')==undefined){
			jQuery('#afd_display_more_options').css('display','none');
			jQuery('#afd_render_wraper').css('display','none');
			jQuery('#clone_wrapper').css('display','none');
		}else{

		}

		jQuery('#afd_global_prop').change(function() {

			if(jQuery('#afd_global_prop').attr('checked')==undefined){
				jQuery('#afd_display_more_options').css('display','none');
				jQuery('#afd_render_wraper').css('display','none');
				jQuery('#clone_wrapper').css('display','none');
			}else{
				jQuery('#afd_display_more_options').css('display','block');
				jQuery('#afd_render_wraper').css('display','block');
				jQuery('#clone_wrapper').css('display','block');
			}

		}); 
			
		</script>
		<?php
		}

	}else{
		global $acf;
		if( $acf == NULL){
			echo __( 'Install Advanced Custom Fields plugin', 'acf_frontend_display' ).'<br/><a href="https://wordpress.org/plugins/advanced-custom-fields/">'.__( 'Plugin website', 'acf_frontend_display' ).'</a>';
		}else{
			echo __( 'Add', 'acf_frontend_display' ).' <a href="'.get_bloginfo('home').'/wp-admin/edit.php?post_type=acf">'.__( 'ACF form', 'acf_frontend_display' ).'</a> '.__( 'to this post', 'acf_frontend_display' );
		}
	}
	// FORM ACTIONS FORCED MESSAGE
	/*if (!defined('Forms_actions')) {
		echo '<div class="alpaca_wpbox_msg">Install more Actions to your form <a class="thickbox" href="'.get_bloginfo('home').'/wp-admin/plugin-install.php?tab=plugin-information&plugin=forms-actions&TB_iframe=true&width=772&height=553" target="_parent">here</a></div>';
	}*/

}

/**
 * When the post is saved, saves our custom data.
 *__( 'Install Advanced Custom Fields plugin', 'acf_frontend_display' ).
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
	$my_data_afd_global_prop = $_POST['afd_global_prop'];

	// Update the meta field in the database.
	update_post_meta( $post_id, '_meta_afd_form_global_prop', $my_data_afd_global_prop );
	update_post_meta( $post_id, '_meta_afd_form_render_box_key', $my_data_afd_render );
	update_post_meta( $post_id, '_meta_afd_form_render_box_alpaca', $my_data_afd_alpaca );

	// update globals into target post 
	if(($_POST['afd_global_prop'] == true)&&($_POST['post_type']=='acf')){
		$rule = get_post_meta($post_id,'rule',true);
		if( $rule['param'] == 'page'){
			$target_post_id = $rule['value'];
			if($target_post_id != ''){
				update_post_meta( $target_post_id, '_meta_afd_form_global_prop', $my_data_afd_global_prop );
				update_post_meta( $target_post_id, '_meta_afd_form_render_box_key', $my_data_afd_render );
				update_post_meta( $target_post_id, '_meta_afd_form_render_box_alpaca', $my_data_afd_alpaca );
			}
		}
		if( $rule['param'] == 'post_type'){
			// The Query
			global $post;
			$the_query = new WP_Query( array('post_type'=>$rule['value']));

			// The Loop
			if ( $the_query->have_posts() ) {
				
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					update_post_meta( $post->ID, '_meta_afd_form_global_prop', $my_data_afd_global_prop );
					update_post_meta( $post->ID, '_meta_afd_form_render_box_key', $my_data_afd_render );
					update_post_meta( $post->ID, '_meta_afd_form_render_box_alpaca', $my_data_afd_alpaca );
				}
				
			} else {
				// no posts found
			}
			/* Restore original Post Data */
			wp_reset_postdata();

		}		


	}



}
add_action( 'save_post', 'afd_save_meta_box_data' );



/* METABOX end ------------------------------------ */


/* DISPLAY filter ------------------------------------ */

function afd_add_form_to_frontend_page($content) {
	global $post;

	//$content = apply_filters('the_content', $post->post_content);
    //$content = str_replace(']]>', ']]&gt;', $content);
    //return $content;

	$must_login_msg = '<div class="message">'.__('Login to edit this post.').'</div>';
    $login_as_author_msg = '<div class="message">'.__('Login as author to edit this post.').'</div>';
	$hash_expired_msg = '<div class="message">'.__('Your activation link was expired.').'</div>';
	$hash_ok_msg = '<div class="message">'.__('Registration completed.').'</div>';
    $user_not_confirmed = '<div class="message">'.__('Your user isnt confirmed by activation link').'</div>';
    // KEY param exist -------------------------------------------------------------
    if($_GET['key'] != ''){

    	$args = json_decode( urldecode ( get_post_meta($post->ID,'_meta_afd_form_render_box_alpaca', true )), true );

		//$user_data = get_userdata( $_GET['user'] );
	    //$code = sha1( $_GET['user'] . $user_data->user_registered );

	    if( $_GET['key'] == get_user_meta( $_GET['user'], '_activation_key', true )){

			delete_user_meta( $_GET['user'], '_activation_key' );
			wp_update_user( array ('ID' => $_GET['user'], 'role' => 'author' ) ) ;
			
			if($args['dependence_three'] == true){
				$hash_ok_msg = '<div class="message">'.$args['display_messages_hash_true'].'</div>';
			}
			$content = $hash_ok_msg;

			if($_GET['redirect_id'] != ''){

				wp_redirect(get_permalink($_GET['redirect_id']));
				exit;
			}

	    }else{

	    	if($args['dependence_three'] == true){
				$hash_expired_msg = '<div class="message">'.$args['display_messages_hash_false'].'</div>';
			}
			$content = $hash_expired_msg;

	    }



	    return $content;
	}
	// ----------------------------------------------------------------------------

    /* check display guardian */

    if( get_post_meta( $post->ID, '_meta_afd_form_render_box_key', true) == 'true'){

		$args = json_decode( urldecode ( get_post_meta($post->ID,'_meta_afd_form_render_box_alpaca', true )), true );
		$display_guardian = true;

		if($args['render_by_id'] != ''){
			$display_guardian = false;			
		}


		// EDIT MODE -------------------------------------------------------------
		// Edit by author only

		if($args['display_edit'] == true) {
			if($_GET['edit'] == 'form'){
				$current_user_id = get_current_user_id();			
				if($_GET['guid'] != $current_user_id){
					$display_guardian = false;
					if($args['dependence_three'] == true){
						$login_as_author_msg = '<div class="message">'.$args['display_messages_author'].'</div>';
					}
					echo $login_as_author_msg;
				}
			}
			

		}

		// LOGIN MODE -------------------------------------------------------------
		// Display for login users
		if($args['display_login'] == true) {
			if ( !is_user_logged_in() ) {
				// user is logged out
				$display_guardian = false;
				if($args['dependence_three'] == true){
					$must_login_msg = '<div class="message">'.$args['display_messages_login'].'</div>';
				}
				echo $must_login_msg;
			}else{
					
				// user is logged in
				$current_user_id = get_current_user_id();
				$user = new WP_User( $current_user_id );
				if($user->roles[0]=='subscriber'){
					$display_guardian = false;
					
					echo $args['dependence_three'];

					if($args['dependence_three'] == true){
						$user_not_confirmed = '<div class="message">'.$args['user_not_confirmed'].'</div>';
					}
					echo $user_not_confirmed;
				}

			}
		}

		/* check form position */
		if($_GET['edit'] != 'form'){
			if($args['in_content_pos'] == 'after'){
				echo '<div class="site-main">'.$content.'<div>';
			}
		}

		if($display_guardian == true){

			unset($args['dependence_one']);
			afd_form_head();
			wp_deregister_style( 'wp-admin' );					

			echo '<div class="site-form">';
				if( empty($args) == true){
					/* afd_frontend_form() is afd_form() extended method */
					afd_frontend_form();
					//acf_form();
				}else{
					/* afd_frontend_form() is afd_form() extended method */
					afd_frontend_form($args);
					//acf_form($args);
				}
			echo '</div>';			
		}

		if($args['render_by_id'] == true){
			unset($args['dependence_one']);
			afd_form_head();
			?>
			<script>
				jQuery.post("<?php echo plugins_url('inc/afd_acf_extend_api.php', __FILE__) ?>" , {'ID':108,'ajax_target':'#<?php echo $args["render_by_id"];?>','args':<?php echo urldecode ( get_post_meta($post->ID,'_meta_afd_form_render_box_alpaca', true )); ?>}, function(response) {
								jQuery('#<?php echo $args["render_by_id"];?>').append(response);
								//renderedForm.setValue(JSON.parse(decodeURIComponent(response)));
							});
			
			</script>
			<?php
		}

		/* check form position */
		if($_GET['edit'] != 'form'){
			if($args['in_content_pos'] == 'before'){
				echo '<div class="site-main">'.$content.'<div>';
			}
		}





		return false;

	}else{

		return $content;

	}

}
add_filter( 'the_content', 'afd_add_form_to_frontend_page', 6);



function acf_js_init()
{
	/* this actior included acf scripts with official documentation:         */
	/* http://www.advancedcustomfields.com/resources/create-a-front-end-form/   */
	/* scripts list: 'jquery','jquery-ui-core','jquery-ui-tabs','jquery-ui-sortable','wp-color-picker','thickbox','media-upload','acf-input','acf-datepicker',	*/
	/* style list: 'thickbox', 'wp-color-picker', 'acf-global', 'acf-input', 'acf-datepicker',	*/

	global $post;
	if( get_post_meta( $post->ID, '_meta_afd_form_render_box_key', true) == 'true'){
		/* Conditional Logic */
		$path = plugins_url() . '/advanced-custom-fields/';
		$output = '';

		$output.="<script type='text/javascript' src='".$path."js/input.min.js?ver=4.3.9'></script>";
		$output.="<link rel='stylesheet' id='acf-input-css'  href='".$path."css/input.css?ver=4.3.9' type='text/css' media='all' />";
		$output.= '<script>acf.o = {}; acf.screen = {}; acf.o.post_id = 0; acf.screen.post_id = 0;</script>';

		echo $output;
	}
}
add_action('wp_head','acf_js_init');
