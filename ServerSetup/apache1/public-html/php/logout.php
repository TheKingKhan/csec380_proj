<?php
include_once('sqlConnect.php');
$stmt = $conn->prepare("UPDATE Users SET sessid=NULL WHERE userid=?;");
$stmt->bind_param("i", $_SESSION['user_ID']);
if(!$stmt->execute()){
    print "Error in executing command";
}
session_destroy();
header("Location: http://localhost");
?>
