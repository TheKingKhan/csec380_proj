<?php
session_start();
$ip = gethostbyname("serversetup_auth_1");
$url = 'http://' . $ip . ':8080/Skitter/LoginServlet';


$data = http_build_query(array('user' => $_POST['user'], 'password' => $_POST['pwd']));
$options = array(
	'http' => array(
	'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	'method'  => 'POST',
	'content' => $data));

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if(is_numeric($result[0])){
	$ids = explode(",", $result);
	$_SESSION['user_ID'] = $ids[0];
	$_SESSION["login_ID"] = substr($ids[1], 0, -1);
	header("Location: http://localhost/home.php?id=" . $_SESSION['user_ID']);
}

die("Authentication failure");
?>
