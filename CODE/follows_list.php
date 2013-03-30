<?php
ob_start();
	include_once('includes/session.php');

chdir(dirname(__FILE__));
	include_once('entities/followers.php');
	include_once('entities/blogpost.php');
	include_once('entities/article.php');
ob_flush();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>WikiBlog | <?php echo $session->username; ?></title>
	 
	<link href="css/reset.css" rel="stylesheet" type="text/css" />
	<link href="css/main.css" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/main.js"></script>
</head>

<body>
	<header>
		<div class="wrapper" id="header">
			<div id="logo">
				<h1><a href="index.php"><img src="img/logo.png" height="40" alt="WikiBlog" /></a></h1>
			</div>
			<div id="searchForm">
				<form action="search.php" method="GET">
					<input type="text" name="query" id="searchBox" />
					<input type="submit" value="Search" name="search" />
				</form>
			</div>
			<div id="userAccount">
				Welcome,
				<?php 
					echo "<a href=\"profile.php?username=".$session->username."\">".$session->username."</a>";
					if($session->userid != -1) {
				?>
				<span class="logout">(<a href="logout.php">logout</a>)<span>
				<?php
					}
				?>
			</div>
		</div>
	</header>
	<div id="content" class="wrapper">
		<?php
			$username = "";
			$userid = -1;

			if(array_key_exists("userid", $_GET)) {
				$userid = intval($_GET['userid']) > 0 ? intval($_GET['userid']) : -1;
			} else if(array_key_exists("username", $_GET)) {
				$username = strlen($_GET['username']) > 0 ? $_GET['username'] : "";
			}

			if($userid > 0) {
				$user_details = $user->getAllDetailsByUserid($userid);
				if(!is_null($user_details)) {
					$username = $user_details['username'];
				} else {
					$no_username = true;
				}
			} else if (strlen($username) > 0) {
				$user_details = $user->getAllDetailsByUsername($username);
				if(!is_null($user_details)) {
					$userid = $user_details['userid'];
				} else {
					$no_userid = true;
				}
			} else {
				$username = $session->username;
				$userid = $session->userid;

				$user_details = $user->getAllDetailsByUserid($userid);
			}

			if(isset($_GET['type'])) {
				switch ($_GET['type']) {
					case 'followers':
						$not_enough_arguments = false;
						$type = 1;
						break;

					case 'followees':
						$type = 2;
						$not_enough_arguments = false;
						break;
					
					default:
						$not_enough_arguments = true;
						break;
				}
			} else {
				$not_enough_arguments = true;
			}
		?>
		<div>
		<?php
			if($not_enough_arguments) {
		?>
			<div class="error big">
				Sorry, the given URL is incorrect.
			</div>
		<?php
			} else {
				if(is_null($user_details)) {
				?>
					<div class="error big">
						Sorry, No profile with 
							<?php 
								if(isset($no_username) && $no_username) {
									echo "user-ID \"".$userid."\"";
								} else {
									echo "username \"".$username."\"";
								}
							?> was found.
					</div>
				<?php
				} else {
				?>
					<div class="big">
						<?php
							echo $username;
							$follows = new Follows($user->getDatabaseClient());
							$blogs = new BlogPost($user->getDatabaseClient());
							$articles = new Article($user->getDatabaseClient());

							if($type == 1) {
								$data = $follows->getFollowers($userid); 
								echo "'s followers are: (Total = ".count($data).")";
							}
							else {
								$data = $follows->getFollowees($userid);
								echo " follows: (Total = ".count($data).")";
							}
						?>
					</div>
					<div id="users_list">
					<?php
						foreach ($data as $key => $value) {
							$users_data = $user->getAllDetailsByUserid($value);
							?>
								<div>
									<img src=
										<?php
											echo $commonfunctions->getProfilePath($users_data['profilepic'], $users_data['username']);
										?>
										alt="profile_pic" width="100" height="100" class="thumbnail_pic"
									/>
									<div>
										<a href="profile.php?username=<?php echo $users_data['username']; ?>">
										<?php
											echo $users_data['firstname']." ".$users_data['lastname'];
										?>
										</a> 
										<?php
											echo "( ".$users_data['username']." )";
										?><br/><br/>
										<?php
											echo "<a href=\"post_list.php?userid=".$value."\">".$blogs->getPostCount($value)." posts</a>, <a href=\"article_list.php?userid=".$value."\">".$articles->getDistinctArticleCount($value)." articles</a>";
										?><br/><br/>
										<?php
											echo substr($users_data['aboutme'], 0, 100)."....";
										?><br/>
										<?php
											foreach ($users_data['interests'] as $key2 => $value2) {
												echo "<a href=\"search?query=".urlencode($value2)."\">".$value2."</a> ";
											}
										?>
									</div>
								</div>
							<?php
						}
					?>
					</div>
				<?php
				}
			}
		?>
		</div>
	</div>
</body>
</html>