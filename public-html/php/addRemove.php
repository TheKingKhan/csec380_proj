<?php
	//This file adds or removes the specified user from
	//the current user's follow list
	//Authenticate user and connect to database
	session_start();
	include_once("sqlConnect.php");

	//If this page is not posted we will kill the connection
	$userid = $_SESSION['user_ID'];
	if(!isset($_POST['addRemoveID'])){
		die("Not POST, fatal error");
	}

	//CSRF token comparison
	$calc = hash_hmac('sha256', $_SESSION['randomString'], $_SESSION['token']);
	if(!hash_equals($calc, $_POST['token'])){
		die("ERROR IN TOKEN");
	}

	//This is the user we will add or remove
	$idToAddRemove = $_POST['addRemoveID'];

	//Grab the following list from the data base
	$friends = "";
	$stmt = $conn->prepare("SELECT following FROM Users WHERE userid = ?;");
	$stmt->bind_param("i", $userid);
	if(!$stmt->execute()){
		print "Error in executing command";
	}

	$stmt->bind_result($friends);
	$stmt->fetch();

	//Find friend in list? -> send to remove friend API
	//Dont' find friend in list? -> send to add friend API
	$friends = explode(",", $friends);
	if(!in_array($idToAddRemove, $friends)){
		$url = "http://serversetup_flask_1:5000/addFriend?id=";
		$url = $url . $idToAddRemove . "&currID=" . $_SESSION['user_ID'];
		$add = file_get_contents($url);
		if(strcmp($add, "Error") == 0){
			die("Error Adding Friend");
		}
	} else {
		$url = "http://serversetup_flask_1:5000/removeFriend?id=";
		$url = $url . $idToAddRemove . "&currID=" . $_SESSION['user_ID'];
		$remove = file_get_contents($url);
		if(strcmp($remove, "Error") == 0){
			die("Error Removing Friend");
		}
	}

	$stmt->close();
	header("Location: http://localhost/listFriends.php?id=" . $userid);
?>