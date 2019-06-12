<?php
include_once("sqlConnect.php");
$stmt = $conn->prepare("DELETE FROM videos WHERE userid = ? and file_name = ?");
$stmt->bind_param("ss", $_SESSION["user_ID"], $_POST["target"]);
$stmt->execute();
header("Location: http://localhost/home.php");
?>
