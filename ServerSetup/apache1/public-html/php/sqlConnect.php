<?php
	session_start();
	$ip = gethostbyname("serversetup_auth_1.serversetup_default");
	$url = "http://". $ip .":8080/Skitter/isAuthenticated?userID=" . $_SESSION['login_ID'];
	$auth = file_get_contents($url);

	if(strpos($auth, "OK") != true){
		header("Location: http://localhost");
	}

	$servername = 'serversetup_mysql_1';
	$dbuname = 'root';
	$dbpass = 'root';
	$dbname = 'Skitter';
	$conn = new mysqli($servername, $dbuname, $dbpass, $dbname);

	if($conn->connect_error){
		die("Connection failed sorry");
	}
?>
