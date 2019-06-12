<?php
	//Connect to database and get some basic information from the uploaded details
	//Also authenticates user
	session_start();
	include_once("sqlConnect.php");
	$userid = NULL;
	$username = $_POST['displayName'];
	$email = $_POST['email'];
	$file = $_FILES['fileToUpload']['name'];

	$calc = hash_hmac('sha256', $_SESSION['randomString'], $_SESSION['token']);
	if(!hash_equals($calc, $_POST['token'])){
		die("ERROR IN TOKEN");
	}

	//Validate the post parameters, they will be NULL if they were not entered.
	$username = validateUsername($username);
	$email = validateEmail($email);
	$file = validateFile($file);

	//Cookie has passed inspection, time to actually execute updates
	if(isset($username)){

		//Check if this is already someone else's username
		$stmt = $conn->prepare("SELECT userid FROM Users WHERE username = ?;");
		$stmt->bind_param("s", $username);

		if(!$stmt->execute()){
			print "Error in executing command";
		}

		$stmt->bind_result($userid);
		$stmt->fetch();
		if(isset($userid)){
			die("Error setting username: username already being used<br>");
		}

		$stmt->close();

		//It is not so we can set this user's username
		$stmt = $conn->prepare("UPDATE Users SET username = ? WHERE userid = ?;");
		$stmt->bind_param("si", $username, $_SESSION['user_ID']);

		if(!$stmt->execute()){
			print "Error in executing command";
		}

		$stmt->close();
	}

	//Store the email, it has already passed inspection
	if(isset($email)){
		$stmt = $conn->prepare("UPDATE Users SET email = ? WHERE userid = ?;");
		$stmt->bind_param("si", $email, $_SESSION['user_ID']);

		if(!$stmt->execute()){
			print "Error in executing command";
		}

		$stmt->close();
	}

	//File has passed initial inspection and is ready to be uploaded
	if(isset($file)){
		$stmt = $conn->prepare("SELECT profile_pic FROM Users WHERE userid = ?;");
		$stmt->bind_param("i", $_SESSION['user_ID']);

		if(!$stmt->execute()){
			print "Error in executing command";
		}

		$stmt->bind_result($profile_pic);
		$stmt->fetch();

		$stmt->close();

		//Not using default profile pic, we need to delete old one
		if(strpos($profile_pic, "../img") != 0){
			unlink($profile_pic);
		}

		//Get the type of file and set up the directory in which we want to store it
		$target_dir = "../uploads/";
		$imgFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));
		$imgFileType = strip_tags($imgFileType);

		//Generate a random name for the file so that any malicious names are disregarded
		$randomName = base64_encode(uniqid('', true));
		$randomName = str_replace("=", '', $randomName);
		$randomName = $randomName . "." . $imgFileType;

		//Prevent any collisions (not likely but needs to be done)
		while(file_exists($randomName)){
			$randomName = base64_encode(uniqid('', true));
			$randomName = str_replace("=", '', $randomName);
			$randomName = $randomName . "." . $imgFileType;
		}

		$target_file = $target_dir . $randomName;
		$imgFileType = strtolower(pathinfo($randomName, PATHINFO_EXTENSION));
		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);

		//Make sure the file has some size and then upload it
		if ($check !== false) {
			move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target_file);

			//Store the file's location in the db
			$stmt = $conn->prepare("UPDATE Users SET profile_pic = ? WHERE userid = ?;");
			$stmt->bind_param("si", $target_file, $_SESSION['user_ID']);

			if(!$stmt->execute()){
				print "Error in executing command";
			}

			$stmt->close();
		} else {
			die("File is not an image.");
		}
	}

	header("Location: http://localhost/home.php?id=" . $_SESSION['user_ID']);

	function validateUsername($unameUnsanitized){
		$unameSanitized = $unameUnsanitized;
		if(strlen($unameSanitized) >= 1){
			return $unameSanitized;
		}
		$unameSanitized = NULL;
		return $unameSanitized;
	}

	function validateEmail($emailUnsanitized){
		$emailSanitized = strip_tags($emailUnsanitized);
		if(strlen($emailSanitized) >= 1){
			if(filter_var($emailSanitized, FILTER_VALIDATE_EMAIL)){
				return $emailSanitized;
			} else {
				die("Email is invalid<br>");
			}
		}

		$emailSanitized = NULL;
		return $emailSanitized;
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
