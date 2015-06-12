<?php
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
	$my_data_afd_render = sanitize_text_field( @$_POST['afd_form_render_box_field'] );
	$my_data_afd_alpaca = @$_POST['afd_alpaca_data'];	
	$my_data_afd_global_prop = @$_POST['afd_global_prop'];

	// Update the meta field in the database.
	update_post_meta( $post_id, '_meta_afd_form_global_prop', $my_data_afd_global_prop );
	update_post_meta( $post_id, '_meta_afd_form_render_box_key', $my_data_afd_render );
	update_post_meta( $post_id, '_meta_afd_form_render_box_alpaca', $my_data_afd_alpaca );

	// update globals into target post 
	if((@$_POST['afd_global_prop'] == true)&&(@$_POST['post_type']=='acf')){
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