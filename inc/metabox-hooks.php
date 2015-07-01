<?php

/* METABOX start ------------------------------------ */

function afd_frontend_add_meta_box() {

	/* build post types array to display ACF frontend metabox */
	//$post_types = get_post_types( '', 'names' );

	//foreach ( $post_types as $key => $value) {
		//$screens[] = $key;
	//}

	//foreach ( $screens as $screen ) {

		/* only editors or administrator can display forms */
		if( current_user_can('edit_others_pages') ) {
			
				$title_box = __( 'Display ACF Form', 'acf_frontend_display' );

			/* display ACF frontend metabox */
			add_meta_box(
				'myplugin_sectionid',
				$title_box,
				'afd_frontend_meta_box_callback',
				$screen,
				'side'
			);
 		}
	//}
}
add_action( 'add_meta_boxes', 'afd_frontend_add_meta_box');

function afd_frontend_meta_box_callback( $post ) {
	global $post;
	if($post->post_type != 'acf'){
		echo 'to set display form properties go to <a href="wp-admin/edit.php?post_type=acf">ACF form</a>';
		return false;
	}
	
	/* check is globals are defined (in first fieldgroup) */
	/* TODO use new function from API afd_attached_forms_array */
	
	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'afd_frontend_meta_box', 'afd_frontend_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	
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
		                    //"title": "More form options?",
		                    "type": "boolean"
		                },		                
/*				        "form_attributes": {
							"type": "string",
							"title": "Form attributes",
							"dependencies": "dependence_one",
							"description": "An array or HTML attributes for the form element."
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
				        },*/
				        "submit_value": {
							"type": "string",
							"title": "Submit value",
							"dependencies": "dependence_one",
							"description": "A string containing the text displayed on the submit button"
				        },
				        "in_content_pos": {
							"type": "string",
							"title": "Position in content",
							"dependencies": "dependence_one",
							"enum": ['disable content','before', 'after']
				        },
				        /*"submit_class": {
							"type": "string",
							"title": "Submit class",
							"dependencies": "dependence_one",
							"description": "Add extra class into submit button"
				        },*/
/*				         "updated_message": {
							"type": "string",
							"title": "Updated Message",
							"dependencies": "dependence_one",							
							"description": "A string message which id displayed above the form after being redirected"
				        },*/
				        // --------------------------------------------------------------------
				       /* "dependence_ajax": {
		                    //"title": "More form options?",
		                    "type": "boolean"
		                },*/
				        "submit_ajax": {
							"type": "boolean",
							"title": " ",
							"dependencies": "dependence_ajax",
							"description": "Asynchronous JavaScript and XML"
				        },				        
				        "ajax_callback": {
							"type": "string",
							"title": "callback AJAX function name",
							"dependencies": "dependence_ajax",
							"description": "name of java script callback function. You couuld add any functions to your scripts like name=function(response);",
							"default": "add_event_callback",
				        },
				        "render_by_id": {
							"type": "string",
							"title": "render_by_id",
							"dependencies": "dependence_ajax",
							"description": "Insert html node id to render form dynamicly. Example '#form_div'. You coud use js function driectly on your files: get_ajax_form( {'ID;:'','ajax_target':'','args':''} );  ID: its your page id with linked ACF form, ajax_target: its html node to render it, args: its your display form properties.",
							//"default": "FA_name(callback)",
				        },

				        /* display options */
/* 						"dependence_two": {
		                    //"title": "More form options?",
		                    "type": "boolean"
		                },*/
				       
				       /* "label_placement": {
							"type": "string",
							"title": "Label placement",
							"dependencies": "dependence_two",
							"enum": ['top', 'left' ,'none']
				        },			*/	       
				       
/*		                "in_content_pos": {
							"type": "string",
							"title": "Position in content",
							"dependencies": "dependence_two",
							"enum": ['disable content','before', 'after']
				        },*/
				        /*"display_template": {
							"type": "string",
							"title": "Form decoration",
							"dependencies": "dependence_two",
							"enum": ['standard ACF', 'Bootstrap']
				        },*/
				        
				        "display_login": {
							"description": "Display form only for login users",
							"type": "boolean"
				        },
/*				        "display_edit": {
							"description": "Display form olny to author. More examples activated form to edit objects.\nEdited mode activated by get:\n &edit=form&guid=user_id",
							"type": "boolean",
							"default":true
				        },*/
				        // messages
/*				        "dependence_three": {
		                    //"title": "More form options?",
		                    "type": "boolean",
		                    "description": "To support this options install 'Forms actions' plugin",
							
		                },*/
		                "display_messages_login": {
							"type": "string",
							"title": "Must login msg",
							"default": "Login to edit this post.",
							"dependencies": "dependence_three",							
				        },
				        "display_messages_author": {
							"type": "string",
							"title": "Must login as author msg.",
							"default": "Login as author to edit this post.",
							"dependencies": "dependence_three",							
				        },
				        "display_messages_after_signon_v_email": {
							"type": "string",
							"title": "Signon with email autentication msg.",
							"default": "Mail with activation link was send to: {user_email}",
							"dependencies": "dependence_three",							
				        },
				        "display_messages_after_signon_mail_title": {
							"type": "string",
							"title": "Signon email title",
							"default": "{blog_name} : Confirmation email",
							"dependencies": "dependence_three",							
				        },
				        "display_messages_after_signon_mail_content": {
							"type": "string",
							"title": "Signon email content",
							"default": "Welcome {user_login} <br/><br/> Please confirm this email by link: {active_link} <br><br>Best<br>{blog_name}",
							"dependencies": "dependence_three",							
				        },
				        "display_messages_hash_true": {
							"type": "string",
							"title": "Signon autentication true",
							"default": "Registration completed.",
							"dependencies": "dependence_three",							
				        },
				        "display_messages_hash_false": {
							"type": "string",
							"title": "Signon autentication false",
							"default": "Your activation link was expired.",
							"dependencies": "dependence_three",							
				        },
				        "user_not_confirmed": {
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
