<?php

//Authenticates the user
session_start();
include_once("sqlConnect.php");

//Make sure Session variables have not been touched
$user_id = strip_tags($_SESSION['user_ID']);

//Strip any tags besides links off of the comments and skit IDs
$content = strip_tags($_POST['commentContent'], "a");
$originalSkitID = strip_tags($_POST['originalSkitID']);
$content = urlencode($content);
$url = "http://serversetup_rails_1:3000/add_skit_reply/result?user_id=" . $user_id . "&content=" . $content . "&originalSkitID=" . $originalSkitID;

//Make sure we had no errors, then send the user on their way
$result = file_get_contents($url);

if(strcmp($result, "Error creating Skit.") == 0){
	die("Error adding skit.");
}

header("Location: http://localhost/home.php?id=" . $user_id);
?>