<?php
	require_once("../../../../wp-load.php");
	global $post;
	global $acf;


	foreach ($_POST['fields'] as $key => $value) {
		update_field($key, afd_parse_value($value), $_POST['post_id']);
	}
	if(get_post_meta( $_POST['post_id'], '_meta_fa_box_alpaca', true )!=''){
		
		$args = json_decode(urldecode(get_post_meta( $_POST['post_id'], '_meta_fa_box_alpaca', true )));
		// [fa_alpaca_properties , post_id , ajax]
		process_actions($args,$_POST['post_id'],true);
	
	}

?>
