<?php
session_start();
$authHost = gethostbyname("serversetup_auth_1.serversetup_default");
$url = "http://". $authHost .":8080/Skitter/isAuthenticated?userID=" . $_SESSION['user_ID'];
$LOAuthHost = "http://" . $authHost . ":8080/Skitter/LogoutServlet";
$auth = file_get_contents($url);

if(strpos($auth, "OK") != true){
	header("Location: http://localhost");
}

include_once("php/sqlConnect.php");
$id_to_get = null;
$vid_to_get = null;
if(isset($_GET['id'])){
	$id_to_get = $_GET['id'];
}
$id_to_get = strip_tags($id_to_get);
if(isset($_GET['v'])){
	$vid_to_get = $_GET['v'];
}
include_once('php/getUserData.php');

?>

<!DOCTYPE html>
<style>
.center {
  text-align: center;
  margin: auto;
  width: 60%;
  border: 3px solid #73AD21;
  padding: 10px;
}
</style>
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
			<div class="container-fluid" id="userBanner">
				<div id="skitterData">
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
			<div class="center">
			<video  controls class="center" width="250">
			<source src="uploads/<?=$id_to_get?>/<?=$vid_to_get?>"
            			type="video/mp4">
    				<p>Sorry, your browser doesn't support embedded videos.</p>
			</video>

			</div>
			

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
