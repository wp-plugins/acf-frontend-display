<?php

require_once("../../../../wp-load.php");
//require_once("../inc/afd_acf_extend_api.php");
global $acf;
global $post;


foreach ($_POST as $key => $value) {
	$rule = get_post_meta($value,rule,true);
	$fieldsArray[$key] = array(
		'group_name' => 'group '.$key,
		'form_name' => get_the_title($value),
		'form_value' => $value,
		'page_name' => get_the_title($rule['value']),
		'page_value' =>$rule['value']

		);
}


echo json_encode($fieldsArray);

//var_dump($fieldsArray);