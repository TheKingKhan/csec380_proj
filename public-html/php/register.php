<?php
	//This will call the Node API to add a skit
	//Authenticates user and checks to see if data has been
	//tampered with.
	//Strips anything but links from post content

	//Set up post variables
	$data = array('name' => strip_tags($_POST['name']), 'email' => strip_tags($_POST['email']), 'pass' => strip_tags($_POST['pass']));
	$options = array(
		'http' => array(
			'header' => "Content-type: application/x-www-form-urlencoded\r\n",
			'method' => "POST",
			'content' => http_build_query($data)
		)
	);

	//POST to the Node API
	$context = stream_context_create($options);
	$ip = gethostbyname("serversetup_auth_1.serversetup_default");
	$result = file_get_contents("http://". $ip .":8080/Skitter/Register", false, $context);

	//Check if it failed to create Skit
	if(strcmp($result, "Error creating Skit.") == 0){
		die("Error adding skit.");
	}

	header("Location: http://localhost/" . $user_id);
?>
