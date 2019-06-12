<?php

if(strpos($_SERVER['REMOTE_ADDR'], "172.18.0.") == 0){
	//Upload is from our servers we gucci.
	$randomName = $_GET['randName'];
	$target_file = "../uploads/" . $randomName;
	move_uploaded_file($_FILES['fileUpload']['tmp_name'], $target_file);
}

?>