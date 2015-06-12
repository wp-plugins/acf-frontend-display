<?php
function init_acfHeader(){
	
	global $post;

	if(($post->post_type == 'page')||($_GET['pid']!='')){
	
		$args_id = afd_form_permision();
		$display_form_checkbox = get_post_meta($args_id[0],'_meta_afd_form_render_box_key',true);

		define("ACF_FRONTEND_DISPLAY", $display_form_checkbox);

		if($display_form_checkbox=='true'){
			acf_form_head();
		}
	
	}

}
//add_action('get_header', 'init_acfHeader');
add_action('wp', 'init_acfHeader');
	
function render_acfForm($content){
	
	if ( ! is_admin() ) {
		
		global $post;

		/* form are display only for pages and posts when edit */
		if(($post->post_type == 'page')||($_GET['pid']!='')){
			
			/* Check frontend display checkbox */
			if( ACF_FRONTEND_DISPLAY=='true' ){

				/* GET OPTIONS */
				$args = json_decode( urldecode ( get_post_meta($args_id[0],'_meta_afd_form_render_box_alpaca', true )), true );

				if($_GET['acf_message'] != ''){
					
					print_r('<div class="msg message">'.$_GET['acf_message'].'</div>');
				
				}else{

					/* render content before form */
					//print_r($content);
				
				}	
				/* edit user */

				$arg = array();
				if($_GET['uid']!=''){
					if ( current_user_can( 'manage_options' ) ) {
					    $uid = $_GET['uid'];
					} else {
					    $uid = get_current_user_id();
					}
					$arg = array('post_id' => 'user_'.$uid);
				}

				/* edit post */
				if($_GET['pid']!=''){
					if ( current_user_can( 'manage_options' ) ) {
					    $pid = $_GET['pid'];
					}else{

						$post = get_post($_GET['pid']);
						if($post->post_author != get_current_user_id()){
							
							$content = '<div class="msg message">Access disabled</div>';
							return $content;
						
						}

					} 
					$arg = array('post_id' => $pid);
				}
				// Display for login users
				if($args['display_login'] == 'true'){
					if ( !is_user_logged_in() ) {
						
						$content = '<div class="msg message">Login to display form</div>';
						return $content;
					}
				}

				if($args['submit_value']!=''){
					$arg['submit_value'] = $args['submit_value'];
				}

				acf_form($arg);
			}

		}	
		
	}
	return $content;

}
add_filter( 'the_content', 'render_acfForm', 6);


/*
global $acf;
if(get_post_meta($id,'_meta_afd_form_render_box_key',true)=='true'){
	$fieldsArray = afd_form_permision();
	foreach ($fieldsArray as $form_group_key => $form_group_value) {
		$fields = apply_filters('acf/field_group/get_fields', array(), $form_group_value);
		foreach ($fields as $field_key => $field_value) {
			//ob_start(); //Start output buffer
			do_action('acf/create_field', $field_value, $id);
			//$output = ob_get_contents();
			//var_dump($output);
			//ob_end_clean();
		}
	}
}*/
