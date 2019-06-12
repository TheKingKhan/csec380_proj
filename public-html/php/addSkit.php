<?php
	//This will call the Node API to add a skit
	//Authenticates user and checks to see if data has been
	//tampered with.
	//Strips anything but links from post content
	session_start();
	include_once("sqlConnect.php");
	$user_id = strip_tags($_SESSION['user_ID']);
	$content = strip_tags($_POST['skitContent'], "a");

	//Set up post variables
	$data = array('user_id' => $user_id, 'content' => $content);
	$options = array(
		'http' => array(
			'header' => "Content-type: application/x-www-form-urlencoded\r\n",
			'method' => "POST",
			'content' => http_build_query($data)
		)
	);

	//POST to the Node API
	$context = stream_context_create($options);
	$result = file_get_contents("http://serversetup_node_1:61234/addSkit", false, $context);

	//Check if it failed to create Skit
	if(strcmp($result, "Error creating Skit.") == 0){
		die("Error adding skit.");
	}

	header("Location: http://localhost/home.php?id=" . $user_id);
?>