<?php
include_once("sqlConnect.php");
$file_name = null;
$display_name = null;
$stmt=$conn->prepare("SELECT file_name, display_name FROM videos WHERE userid = ?;");
$stmt->bind_param("i", $_SESSION['user_ID']);
$stmt->execute();
$stmt->bind_result($file_name, $display_name);
$allFiles = new ArrayObject(array());
$allNames = new ArrayObject(array());
$numVids = 0;
while($stmt->fetch()){
	$allFiles->append($file_name);
	$allNames->append($display_name);
	$numVids++;
}
$stmt->close();
?>
