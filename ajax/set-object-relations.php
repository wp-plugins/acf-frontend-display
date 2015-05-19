<?php
	require_once("../../../../wp-load.php");
	global $post;

	// data['post_id']
	// data['rel_meta_name']
	//echo $_POST['post_id'];

/*	var data_shema1 = { 
		'args':{
			'post_id':'',
			'rel_meta_name':'',
			'override': true // true create new unique array, flase add new elements and dont delete old
			'method' : 'GET' // GET SET
		},
		'input':{
			
		},
		'main_output':{
			'post_id':'id_spotkanie',
			'user_id':'id_user'
		},
		'post_output':{
			'x':'y'
		},
		'user_output':{
			'x':'y'
		},
		'false_elements':{
			'avatar':''
		}
	} */

	/* check is meta exist */
	$guardian = true;
	$result = get_post_meta($_POST['post_id'], $_POST['rel_meta_name'], false);
	//var_dump($result);
	$output = array();
	$counter = 0;
	$key_to_remove;

	foreach ($result as $key => $value) {
		
		if($value == $_POST['meta_value']){
			$guardian = false;
		
		}else{

			$avatar = get_user_meta($value,'avatar',true);
			$output[$key]["id_spotkanie"] = $_POST['post_id'];
			$output[$key]["id_user"] = $value;
			$output[$key]["avatar"] = $avatar;
			$counter++;

		}
	}

	$true_meta_name = substr($_POST['rel_meta_name'], 0, 4);
	$true_meta_array = get_post_meta($_POST['post_id'],$true_meta_name,true);

	if($guardian == true){

		$counter++;
		add_post_meta($_POST['post_id'], $_POST['rel_meta_name'], $_POST['meta_value'], false);

		$avatar = get_user_meta($_POST['meta_value'],'avatar',true);
		$output[$counter]["id_spotkanie"] = $_POST['post_id'];
		$output[$counter]["id_user"] = $_POST['meta_value'];
		$output[$counter]["avatar"] = $avatar;

		$true_meta_array.= $true_meta_array.','.$_POST['meta_value'];

	}else{

		$true_meta_array = explode( ',', $true_meta_array );
		unset($true_meta_array[$_POST['meta_value']]);
		$true_meta_array = implode(",", $true_meta_array);

		update_post_meta($_POST['post_id'],$true_meta_name,$true_meta_array,true);

		delete_post_meta($_POST['post_id'], $_POST['rel_meta_name'],  $_POST['meta_value']);
		//unset($output[$key_to_remove]);
	}

	//var_dump($output);
	echo json_encode($output);	

