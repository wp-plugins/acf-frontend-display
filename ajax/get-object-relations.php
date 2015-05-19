<?php
	require_once("../../../../wp-load.php");
	global $post;

	// data['post_id']
	// data['rel_meta_name']
	//echo $_POST['post_id'];
	$result = get_post_meta($_POST['post_id'], $_POST['rel_meta_name'], false);
	//var_dump($result);
	$output = array();
	foreach ($result as $key => $value) {
		$avatar = get_user_meta($value,'avatar',true);
		$output[$key]["id_spotkanie"] = $_POST['post_id'];
		$output[$key]["id_user"] = $value;
		if($avatar == ''){
	      $output[$key]["avatar"] = get_template_directory_uri()."/img/blankuser.gif";
	    }else{
	      $output[$key]["avatar"] = $avatar;
	    }

	}
	//var_dump($output);
	echo json_encode($output);	

