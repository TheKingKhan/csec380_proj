<?php
	//Connect to database and get some basic information from the uploaded details
	//Also authenticates user
	session_start();
	include_once("sqlConnect.php");
	$userid = NULL;
	$video_name = $_POST['video_name'];
	$video_link = $_POST['video_link'];
	$file = $_FILES['file_to_upload']['name'];
	$src = $_FILES['file_to_upload']['tmp_name'];
	$err = $_FILES['file_to_upload']['error'];
#	echo $video_link;
#	$calc = hash_hmuac('sha256', $_SESSION['randomString'], $_SESSION['token']);
#	if(!hash_equals($calc, $_POST['token'])){
#		die("ERROR IN TOKEN");
#	}

	//Validate the post parameters, they will be NULL if they were not entered.
#	$file = validateFile($file);
#	$video_name = validateUsername($video_name);

	//Cookie has passed inspection, time to actually execute updates
	if(isset($video_name)){
		//File has passed initial inspection and is ready to be uploaded
		$target_dir = "../uploads/" . $_SESSION['user_ID'] . "/";
#			$vidFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));
#			$vidFileType = strip_tags($vidFileType);
			//Generate a random name for the file so that any malicious
		$randomName = base64_encode(uniqid('', true));
		$randomName = str_replace("=", '', $randomName);
		$randomName = $randomName . ".mp4";
		$target_file = $target_dir . $randomName;
		if (isset($file)){
			move_uploaded_file($_FILES['file_to_upload']['tmp_name'], $target_file);
		}
		if(isset($video_link)){
			if(strpos($video_link, "://")){
				$res = file_put_contents($target_file, fopen($video_link, "r"));
			}
		}
		$stmt = $conn->prepare("INSERT videos VALUES(?, ?, ?)");
		$stmt->bind_param("iss", $_SESSION['user_ID'], $randomName, $video_name);
		if(!$stmt->execute()){
			echo "Error in executing command";
		}
		$stmt->close();

	}
	header("Location: http://localhost/home.php");

	function validateUsername($unameUnsanitized){
		$unameSanitized = strip_tags($unameUnsanitized);
		if(strlen($unameSanitized) >= 1){
			return $unameSanitized;
		}
		$unameSanitized = NULL;
		return $unameSanitized;
	}

	function validateFile($fileUnsanitized){
		$fileSanitized = strip_tags($fileUnsanitized);
		if(strlen($fileSanitized) >= 1){
			return $fileSanitized;
		}

		$fileSanitized = NULL;
		return $fileSanitized;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Skitter</title>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/home.css">
		<link rel="apple-touch-icon" sizes="180x180" href="img/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="img/favicon-16x16.png">
		<link rel="manifest" href="img/site.webmanifest">
		<link rel="mask-icon" href="img/safari-pinned-tab.svg" color="#5bbad5">
		<meta name="msapplication-TileColor" content="#da532c">
		<meta name="theme-color" content="#ffffff">
	</head>
	<body>

	</body>
</html>
