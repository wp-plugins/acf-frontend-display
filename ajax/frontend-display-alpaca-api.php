<?php
	require_once("../../../../wp-load.php");
	$args = get_post_meta($_POST['post_id'],'_meta_afd_form_render_box_alpaca', true );
	echo $args;
?>