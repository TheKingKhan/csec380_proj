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
					<h4 id="title">Friends' Posts</h4>
				</div>
				<?php

					//Lists the top 5 most recent friend skits on side bar
					$stmt = $conn->prepare("SELECT following  FROM Users WHERE userid = ?;");
					$stmt->bind_param("i", $_SESSION['user_ID']);

					if(!$stmt->execute()){
						print "Error in executing command";
					}

					$stmt->bind_result($friends);
					$stmt->fetch();
					$stmt->close();

					if(strlen($friends) > 0){
					$url = "http://serversetup_node_1:61234/getSkits?ids=";
					if($friends[0] == ','){
						$friends = substr($friends, 1);
					}
					$url = $url . $friends;
					$skitData = file_get_contents($url);
					$i = 0;

					//Split them by new line characters
					$skits = preg_split("/((\r?\n)|(\r\n?))/", $skitData);
					while($i < 5){
						$line = $skits[$i];
						if(strlen($line) == 0)
							break;
						//Split the lines by comma and then siphen the data we need from themn
						$line_arr = json_decode($line, true);
						$skitOwner = $line_arr[0];

						$skitUsername = "";
						$skitProfilePic = "";

						//Query the DB for the username and profile picture location from the users
						$stmt = $conn->prepare("SELECT username, profile_pic  FROM Users WHERE userid = ?;");
						$stmt->bind_param("i", $skitOwner);

						if(!$stmt->execute()){
							print "Error in executing command";
						}

						$stmt->bind_result($skitUsername, $skitProfilePic);
						$stmt->fetch();
						$stmt->close();
				?>
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
						$i = $i + 1;
					}
				}

				?>
				<div id="seeMoreButton">
					<a href="listFriends.php?id=<?=$id_to_get?>"><button type="button" id="viewMoreButton">Friends</button></a>
				</div>
			</div>

			<div class="container-fluid" id="userPosts">
				<div class="container-fluid" id="addPost">
					<form action="php/addSkit.php" method="post">
						<p>What are you thinking?</p>
						<input type="text" id="skitContent" placeholder="It's a nice day" name="skitContent" maxlength="140">
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

					//Get the skit data
					if(strlen($friends) > 0){
						$url = "http://serversetup_node_1:61234/getSkits?ids=";
						if($friends[0] == ','){
							$friends = substr($friends, 1);
						}
						$url = $url . $friends . "," . $_SESSION['user_ID'];
					} else {
						$url = "http://serversetup_node_1:61234/getSkits?ids=" . $_SESSION['user_ID'];
					}
					$skitData = file_get_contents($url);
					foreach(preg_split("/((\r?\n)|(\r\n?))/", $skitData) as $line){

						//If the line of data is empty just leave the for loop because we have reached illegal data.
						if(strlen($line) == 0)
							break;

						$line_arr = json_decode($line, true);
						$replyList = $line_arr[4];
						$skitOwner = $line_arr[0];

						//If the owner of this skit we are loading is not the current user
						//We will need to query for some basic information about it
						if($skitOwner != $thisUserID){
							$skitUsername = "";
							$skitProfilePic = "";
							$stmt = $conn->prepare("SELECT username, profile_pic  FROM Users WHERE userid = ?;");
							$stmt->bind_param("i", $skitOwner);

							if(!$stmt->execute()){
								print "Error in executing command";
							}

							$stmt->bind_result($username, $profile_pic);
							$stmt->fetch();
							if(isset($userid)){
								die("Error setting username: username already being used<br>");
							}

							$stmt->close();
						} else {

							//Move our data back into the variables
							$profile_pic = $thisProfilePic;
							$username = $thisUsername;
						}

						?>
						<div id="post" class="container-fluid">
							<div id="personalBanner">
								<div id="bannerData">
									<img id="postPic" src="<?=$profile_pic?>" />
									<p id="postusername"><strong><?=$username?></strong></p>
								</div>
							</div>
							<div id="data">
								<p id="postContent"><?=$line_arr[1]?></p>
								<div id="personalPostData">
									<div id="personalPostComment">
									<?php
										if($line_arr[3] != -1){
											$url = "http://serversetup_node_1:61234/getReply?id=";
											$url = $url . $line_arr[3];
											$originalData = file_get_contents($url);
											$originalData = json_decode($originalData, true);
											$stmt = $conn->prepare("SELECT username, profile_pic  FROM Users WHERE userid = ?;");
											$stmt->bind_param("i", $originalData[0]);

											if(!$stmt->execute()){
												print "Error in executing command";
											}

											$stmt->bind_result($username, $profile_pic);
											$stmt->fetch();
											if(strlen($originalData[0]) == 0){
												$originalData[1] = "Original Skit not available, sorry.";
												$profile_pic = "img/missingSkit.svg";
												$username = "";
											}

											$stmt->close();
									?>
										<div id="originalPost">
											<p><strong>Original Skit:</strong></p>
											<img id="replyPic" src="<?=$profile_pic?>" />
											<p id="replyUsername"><strong><?=$username?></strong></p>
											<p id="replyContent"><?=$originalData[1]?></p>
										</div>
									<?php
										}
										if($replyList[0] != -1){
											foreach($replyList as $replyID){
												$url = "http://serversetup_node_1:61234/getReply?id=";
												$url = $url . $replyID;
												$replyData = file_get_contents($url);
												$replyData = json_decode($replyData, true);
												$stmt = $conn->prepare("SELECT username, profile_pic  FROM Users WHERE userid = ?;");
												$stmt->bind_param("i", $replyData[0]);

												if(!$stmt->execute()){
													print "Error in executing command";
												}

												$stmt->bind_result($username, $profile_pic);
												$stmt->fetch();
												if(isset($userid)){
													die("Error setting username: username already being used<br>");
												}

												$stmt->close();
									?>
										<div class="comment">
											<img id="replyPic" src="<?=$profile_pic?>" />
											<p id="replyUsername"><strong><?=$username?></strong></p>
											<p id="replyContent"><?=$replyData[1]?></p>
										</div>
									<?php
											}
										}?>
									</div>
									<?php
										/*
											If this my skit, then I can delete it.
										*/
										if($_SESSION['user_ID'] == $skitOwner){
											echo "
											<div id=\"deleteButtonDiv\">
												<form action=\"php/deleteSkit.php\" method=\"post\">
													<input type=\"hidden\" name=\"token\" value=\"" . hash_hmac('sha256', $randomString, $token) . "\">
													<input type=\"hidden\" name=\"skitID\" value=\"$line_arr[2]\">
													<button type=\"submit\" id=\"deleteButton\">Delete</button>
												</form>
											</div>";
										} else {

										/*
											This is not my skit, so I can comment on it.
										*/
										?>
											<div id="comment">
												<form action="php/addComment.php" method="post">
													<input type="text" name="commentContent">
													<input type="hidden" name="originalSkitID" value="<?=$line_arr[2]?>">
													<button type="submit" id="addComment">Add Comment</button>
												</form>
											</div>
									<?php
										}
									?>
								</div>
							</div>
						</div>
					<?php
					}
				} else {
					//We are not on our own page so we only want to see that user's posts
					//Not their friends or our friends or our posts
					$url = "http://serversetup_node_1:61234/getSkits?ids=";
					$url = $url . $id_to_get;
					$skitData = file_get_contents($url);
					$currPageUser = -1;
					$currPagePic = "";
					$stmt = $conn->prepare("SELECT username, profile_pic  FROM Users WHERE userid = ?;");
					$stmt->bind_param("i", $id_to_get);

					if(!$stmt->execute()){
						print "Error in executing command";
					}

					$stmt->bind_result($currPageUser, $currPagePic);
					$stmt->fetch();
					$stmt->close();
					foreach(preg_split("/((\r?\n)|(\r\n?))/", $skitData) as $line){
						if(strlen($line) == 0)
							break;

						$line_arr = json_decode($line, true);
						$replyList = $line_arr[4];
						$skitOwner = $line_arr[0];
						?>
						<div id="post" class="container-fluid">
							<div id="personalBanner">
								<div id="bannerData">
									<img id="postPic" src="<?=$currPagePic?>" />
									<p id="postusername"><strong><?=$currPageUser?></strong></p>
								</div>
							</div>
							<div id="data">
								<p id="postContent"><?=$line_arr[1]?></p>
								<div id="personalPostData">
									<div id="personalPostComment">
										<?php
											if($line_arr[3] != -1){
												$url = "http://serversetup_node_1:61234/getReply?id=";
												$url = $url . $line_arr[3];
												$originalData = file_get_contents($url);
												$originalData = json_decode($originalData, true);
												$stmt = $conn->prepare("SELECT username, profile_pic  FROM Users WHERE userid = ?;");
												$stmt->bind_param("i", $originalData[0]);

												if(!$stmt->execute()){
													print "Error in executing command";
												}

												$stmt->bind_result($username, $profile_pic);
												$stmt->fetch();
												if(strlen($originalData[0]) == 0){
													$originalData[1] = "Original Skit not available, sorry.";
													$profile_pic = "img/missingSkit.svg";
													$username = "";
												}
												$stmt->close();
										?>
											<div id="originalPost">
												<p><strong>Original Skit:</strong></p>
												<img id="replyPic" src="<?=$profile_pic?>" />
												<p id="replyUsername"><strong><?=$username?></strong></p>
												<p id="replyContent"><?=html_entity_decode($originalData[1]);?></p>
											</div>
										<?php
											}
											if($replyList[0] != -1){
												foreach($replyList as $replyID){
													$url = "http://serversetup_node_1:61234/getReply?id=";
													$url = $url . $replyID;
													$replyData = file_get_contents($url);
													$replyData = json_decode($replyData, true);
													$stmt = $conn->prepare("SELECT username, profile_pic  FROM Users WHERE userid = ?;");
													$stmt->bind_param("i", $replyData[0]);

													if(!$stmt->execute()){
														print "Error in executing command";
													}

													$stmt->bind_result($username, $profile_pic);
													$stmt->fetch();
													if(isset($userid)){
														die("Error setting username: username already being used<br>");
													}

													$stmt->close();
										?>
											<div class="comment">
												<img id="replyPic" src="<?=$profile_pic?>" />
												<p id="replyUsername"><strong><?=$username?></strong></p>
												<p id="replyContent"><?=$replyData[1]?></p>
											</div>
										<?php
												}
											} else {
												echo "Sorry no friends";
											}?>
										</div>
										<?php
											/*
												If this my skit, then I can delete it.
											*/
											if($_SESSION['user_ID'] == $skitOwner){
												echo "
												<div id=\"deleteButtonDiv\">
													<form action=\"php/deleteSkit.php\" method=\"post\">
														<input type=\"hidden\" name=\"token\" value=\"" . hash_hmac('sha256', $randomString, $token) . "\">
														<input type=\"hidden\" name=\"skitID\" value=\"$line_arr[2]\">
														<button type=\"submit\" id=\"deleteButton\">Delete</button>
													</form>
												</div>";
											} else {

											/*
												This is not my skit, so I can comment on it.
											*/
											?>
												<div id="comment">
													<form action="php/addComment.php" method="post">
														<input type="text" name="commentContent">
														<input type="hidden" name="originalSkitID" value="<?=$line_arr[2]?>">
														<button type="submit" id="addComment">Add Comment</button>
													</form>
												</div>
										<?php
											}
										?>
									</div>
								</div>
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
