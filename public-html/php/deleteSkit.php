<?php

	//Authenticate user
	session_start();
	include_once("sqlConnect.php");

	//Make sure the skit is only being deleted by the button being clicked
	$calc = hash_hmac('sha256', $_SESSION['randomString'], $_SESSION['token']);
	if(!hash_equals($calc, $_POST['token'])){
		die("ERROR IN TOKEN");
	}

	//Strip the user id and skit id of any tampering and POST to Node API
	$user_id = strip_tags($_SESSION['user_ID']);
	$skitID = strip_tags($_POST['skitID']);
	$data = array('user_id' => $user_id, 'skitID' => $skitID);
	$options = array(
		'http' => array(
			'header' => "Content-type: application/x-www-form-urlencoded\r\n",
			'method' => "POST",
			'content' => http_build_query($data)
		)
	);

	//Performs the POST and makes sure we have no errors
	$context = stream_context_create($options);
	$result = file_get_contents("http://serversetup_node_1:61234/deleteSkit", false, $context);

	if(strcmp($result, "Error deleting Skit.") == 0){
		die("Error removing skit.");
	}

	header("Location: http://localhost/home.php?id=" . $user_id);
?>