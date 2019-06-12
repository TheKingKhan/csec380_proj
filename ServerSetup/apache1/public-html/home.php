<?php
session_start();
$authHost = gethostbyname("serversetup_auth_1.serversetup_default");
$url = "http://". $authHost .":8080/Skitter/isAuthenticated?userID=" . $_SESSION['login_ID'];
$LOAuthHost = "http://" . $authHost . ":8080/Skitter/LogoutServlet";
$auth = file_get_contents($url);

if(strpos($auth, "OK") != true){
	header("Location: http://localhost");
}

include_once("php/sqlConnect.php");
$id_to_get = $_SESSION['user_ID'];
if(isset($_GET['id'])){
	$id_to_get = $_GET['id'];
}
$id_to_get = strip_tags($id_to_get);
include_once('php/getUserData.php');

$_SESSION['token'] = bin2hex(random_bytes(32));
$_SESSION['randomString'] = bin2hex(random_bytes(32));
$_SESSION['deleteToken'] = bin2hex(random_bytes(32));
$_SESSION['username'] = $username;
$_SESSION['email'] = $email;
$token = $_SESSION['token'];
$randomString = $_SESSION['randomString'];
$deleteToken = $_SESSION['deleteToken'];
include_once("php/getAllVids.php");
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
	<div class="container-fluid" id="mainContainer">
		<div class="container-fluid">
			<div id="blur">
				<div class="container-fluid" id="inputArea">
					<h2 id="settingsHeader">Settings</h2>
					<div id="field">
						<form action="php/settings.php" method="post" enctype="multipart/form-data">
							<p>Display Name:</p>
							<input type="text" id="displayName" placeholder="Enter Display Name" name="displayName">
							<p>Email:</p>
							<input type="text" id="email" placeholder="Enter Email Address" name="email">
							<p>Profile Pic:</p>
							<input type="file" name="fileToUpload" id="fileToUpload">
							<input type="hidden" name="token" value="<?= hash_hmac('sha256', $randomString, $token)?>">
							<button type="submit" id="submitButton"><span>Submit</span></button>
							<button type="button" id="exitButton"><span>Close</span></button>
						</form>
					</div>
				</div>
			</div>
			<div class="container-fluid" id="userBanner">
				<div id="skitterData">
					<button id="getSettings" type="button">Settings</button>
					<a href="/home.php?id=<?=$_SESSION['user_ID']?>"><button id="goHome" type="button" >Home</button></a>
					<a href="/php/logout.php"><button id="logout" type="button">Logout</button></a>

				</div>
				<div id="userData">
					<div id="usernamediv">
						<h2 id="username">
							<?=$_SESSION['username'];?>
						</h2>
						<br/>
						<h5 id="email">
							<?= $_SESSION['email'];?>
						</h5>
					</div>
					<div id="bannerProfPic">
						<img id="userProfilePic" src="<?=$profile_pic?>" />
					</div>
				</div>
			</div>

			<div class="container-fluid" id="friendsPosts">
				<div id="about">
					<img id="skitterLogo" src="img/bird.svg" />
					<h4 id="title">Friends' Uploads</h4>
				</div>
<!--				<?php

					//Lists the top 5 most recent friend skits on side bar
#					$stmt = $conn->prepare("SELECT following  FROM Users WHERE userid = ?;");
#					$stmt->bind_param("i", $_SESSION['user_ID']);
#
#					if(!$stmt->execute()){
#						print "Error in executing command";
#					}
#
#					$stmt->bind_result($friends);
#					$stmt->fetch();
#					$stmt->close();
#
#					if(strlen($friends) > 0){
#					if($friends[0] == ','){
#						$friends = substr($friends, 1);
#					}
#					while($i < 5){
#						$stmt = $conn->prepare("SELECT username, profile_pic  FROM Users WHERE userid = ?;");
#						$stmt->bind_param("i", $skitOwner);
#
#						$stmt->fetch();
#						$stmt->close();
#				?>
				<a href="/home.php?id=<?= $skitOwner?>" class="sideBarSkit">
					<div id="friendPost" class="container">
						<div id="banner">
							<img id="friendProfilePic" src="<?=$skitProfilePic?>" />
							<h5><?=$skitUsername?></h5>
						</div>
						<div id="content">
							<p id="postContent"><?=$line_arr[1]?></p>
						</div>
					</div>
				</a>
				<?php
#						$i = $i + 1;
#					}
#				}

?>
-->
				<div id="seeMoreButton">
					<a href="listFriends.php?id=<?=$id_to_get?>"><button type="button" id="viewMoreButton">Friends</button></a>
				</div>
			</div>

			<div class="container-fluid" id="userPosts">
				<div class="container-fluid" id="addPost">
					<form action="php/addUpload.php" method="post" enctype="multipart/form-data">
						<p>Share a video!</p>
						<input type="text" id="video_name" placeholder="Video Display Name Here!"name="video_name">
						<input type="text" id="video_link" placeholder="Video URL here!" name="video_link" maxlength="140">
						<input type="file" name="file_to_upload" id="video_file">
						<button type="submit" id="submitButton"><span>Submit</span></button>
					</form>
				</div>
				<?php
				/*
					If the person's home we are trying to get is the Current User's page
					Then and only then will we load in our Friend's skits as well as their own
				*/
				if($_SESSION['user_ID'] == $id_to_get){

					//Add in some constants so we don't have to query the DB everytime we add in a skit
					//only when it is a different person than ourself
					$thisUsername = $username;
					$thisProfilePic = $profile_pic;
					$thisUserID = strval($_SESSION['user_ID']);
					$index = 0;
					for($index=0;$index<$numVids;$index++){
						$profile_pic = $thisProfilePic;
						$username = $thisUsername;
						$display_name = $allNames[$index];
						$file_name = $allFiles[$index];
						$link = "showTime.php?id=" . $id_to_get . "&v=" . $file_name;
				?>
						<div id="post" class="container-fluid">
							<div id="personalBanner">
								<div id="bannerData">
									<img id="postPic" src="<?=$profile_pic?>" />
									<p id="postusername"><strong><?=$username?></strong></p>
								</div>
							</div>
							<div id="data">
								<a href=<?=$link?> id="postContent"><?=$display_name?></a>
								<video  controls class="center" width="250">
								<source src="uploads/<?=$id_to_get?>/<?=$file_name?>"
            							type="video/mp4">
    								<p>Sorry, your browser doesn't support embedded videos.</p>
								</video>
							</div>
							<div id= "deleteUpload">
								<form action="php/deleteUpload.php" method="post">
								<input type="hidden" name="target" value="<?=$file_name?>">
								<button type="submit" id="delButton"><span>Delete Video</span></button>
								</form>
							</div>
						</div>
				<?php
					}
				} else {
					include_once("php/getSomeVids.php");
					for($index=0;$index<$numVids;$index++){
						$profile_pic = $thisProfilePic;
						$username = $thisUsername;
						$display_name = $allNames[$index];
						$file_name = $allFiles[$index];
						$link = "showTime.php?id=" . $id_to_get . "&v=" . $file_name;
				?>

						<div id="post" class="container-fluid">
							<div id="personalBanner">
								<div id="bannerData">
									<img id="postPic" src="<?=$profile_pic?>" />
									<p id="postusername"><strong><?=$username?></strong></p>
								</div>
							</div>
							<div id="data">
								<a href=<?=$link?> id="postContent"><?=$display_name?></a>
								<video  controls class="center" width="250">
								<source src="uploads/<?=$id_to_get?>/<?=$file_name?>"
            							type="video/mp4">
    								<p>Sorry, your browser doesn't support embedded videos.</p>
								</video>
						</div>

				<?php
					}
				}
				?>
				<div id="credits">
					<div>Icons made by <a href="https://www.flaticon.com/authors/smashicons" title="Smashicons">Smashicons</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
					<div>Icons made by <a href="https://www.flaticon.com/authors/pixel-buddha" title="Pixel Buddha">Pixel Buddha</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
					<div>Icons made by <a href="https://www.flaticon.com/authors/dave-gandy" title="Dave Gandy">Dave Gandy</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
				</div>
			</div>
		</div>
		<script src="js/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="js/home.js"></script>
	</body>
	</html>
