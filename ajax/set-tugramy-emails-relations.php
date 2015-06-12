<?php
	require_once("../../../../wp-load.php");
	global $post;

	$current_user = get_current_user_id();

	if($current_user == 0){
		echo '{"warning":"access terminated - you must login"}';
		die();
	}

	$rel_meta_name = 'rel_'.$_POST['meta_name'];
	$result = get_post_meta($_POST['post_id'], $rel_meta_name, false);


    $value = preg_replace('/\s+/', '', $_POST['value']);
    $metas_array = explode(",", $value);
    $metas_array = array_unique($metas_array);
    foreach ($metas_array as $key => $value) {
		if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
		   //echo $value;
		    $val_emails[$key] = $value;
		}else{
			//echo 'wrong email!!!';
		}
	}
	
	
	
	$my_meta = get_post_meta($_POST['post_id'],$_POST['meta_name'],true);
	$my_meta = preg_replace('/\s+/', '', $my_meta);
	$my_meta_array = explode(",", $my_meta);


	$merge = array_merge($val_emails, $my_meta_array);
	$merge = array_unique($merge);

	build_namy_metas($merge);
	
	$merge = implode(",",$merge);
	update_post_meta($_POST['post_id'],$_POST['meta_name'],$merge,false);



	function build_namy_metas($array){
		delete_post_meta($_POST['post_id'], 'ref_zaproszeni_emails');
		foreach ($array as $key => $value) {
			add_post_meta($_POST['post_id'],'ref_zaproszeni_emails',$value,false);

		}
	}

	add_filter( 'wp_mail_content_type', 'set_html_content_type' );

	function set_html_content_type() {
		return "text/html";
	}

	send_emails($_POST['post_id'], $val_emails);

	var_dump($val_emails);

	function send_emails($eventid,$emails){
		
		// get current user info and email
		$userid = get_current_user_id();
		$userinfo = get_userdata( $userid );
		$usermail = $userinfo->user_email;
		
		// determine user credentials for use in mail
		if (!empty($userinfo->first_name)) {
			$username = $userinfo->first_name;
			if (!empty($userinfo->last_name)) {
				$userlast = $userinfo->last_name;
				$usercred = $username . " " . $userlast;
			} else {
				$usercred = $username;
			}
		} else {
			$usercred = $userinfo->user_login;
		}

		//get event url
		$eventurl = get_permalink( $eventid );
		
		//get event slug
		$eventdata = get_post($eventid, ARRAY_A);
		$eventslug = $eventdata['post_name'];

		//get event title
		$eventtitle = get_the_title( $eventid );

		//get site url
		$siteurl = site_url();

		//email message template
		$headers = array('Content-Type: text/html; charset=UTF-8');
		$headers[] = 'From: TuGramy <kontakt@tugramy.pl>';
		$headers[] = "Reply-To:" . $usercred . "<".$usermail.">";
		

		$subject = "[TuGramy] " . $usercred . " zaprasza Cię na " . $eventtitle;

		$message = "<html><head><title>". $subject . "</title></head><body>Twój znajomy " . $usercred . " zaprosił Cię do udziału w wydarzeniu <a href='".$siteurl ."/mapa/#".$eventslug." '>" . $eventtitle . "</a></body></html>";

		//work on email array
		foreach ($emails as $to) {
			wp_mail( $to, $subject, $message, $headers );
		}
	}
	
