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

		?>
		<div id="profile">
			<?php
				if(!is_null($user_details)) {
			?>
			<div class="profile_functions">
				<img src=
					<?php
						echo $commonfunctions->getProfilePath($user_details['profilepic'], $username);
					?>
					alt="profile_pic" width="296" height="296" class="big_pic"
				/>
				<div id="profile_follows">
					<?php
						$follows = new Follows($user->getDatabaseClient());
						$followers = $follows->getFollowersCount($userid);
						$followees = $follows->getFolloweesCount($userid);
					?>
					<div style="float:left; width: 45%;">
						<a href="follows_list.php?type=followers&amp;userid=<?php echo $userid; ?>" id="followers_summary"><?php echo $followers; ?><br/>followers</a>
					</div>
					<div style="float:right; width: 45%;">
						<a href="follows_list.php?type=followees&amp;userid=<?php echo $userid; ?>" id="followees_summary">follows<br/><?php echo $followees; ?></a>
					</div>
					<div class="clear"></div>
				</div>
				<div id="profile_writes">
					<?php
						$blogs = new BlogPost($user->getDatabaseClient());
						$blogCount = $blogs->getPostCount($userid);

						$articles = new Article($user->getDatabaseClient());
						$articleCount = $articles->getAllArticleCount($userid);
					?>
					<div style="float:left; width: 45%;">
						<a href="article_list.php?userid=<?php echo $userid; ?>"><?php echo $articleCount; ?><br/>articles</a>
					</div>
					<div style="float:right; width: 45%;">
						<a href="post_list.php?userid=<?php echo $userid; ?>"><?php echo $blogCount; ?><br/>posts</a>
					</div>
					<div class="clear"></div>
				</div>
				<?php
					if($userid == $session->userid) {
				?>
					<div>
						<a href="write?type=article">write ARTICLE</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="write?type=post">write BLOGPOST</a>
					</div>
				<?php
					} else if($follows->checkFollows($session->userid, $userid)) {
				?>
					<div><button id="unfollow_user" data-follower=<?php echo "\"".$session->userid."\""; ?> data-followee=<?php echo "\"".$userid."\""; ?>>Unfollow User</button></div>
				<?php
					} else {
				?>
					<div><button id="follow_user" data-follower=<?php echo "\"".$session->userid."\""; ?> data-followee=<?php echo "\"".$userid."\""; ?>>Follow User</button></div>
				<?php
					}
				?>
			</div>
			<div class="profile_details">
				<table>
					<tr>
						<td colspan="2" style="font-size:200%;"><?php echo $user_details['firstname']." ".$user_details['lastname']; ?>
							<?php
								if($userid == $session->userid) {
							?>
								<span style="float:right;"><a href="edit_profile.php"><button>Edit Profile</button></a></span>
							<?php
								}
							?>
						</td>
					</tr>
					<tr>
						<td>Username</td>
						<td><?php echo $username; ?></td>
					</tr>
					<tr>
						<td>Email-ID</td>
						<td><?php echo $user_details['emailid']; ?></td>
					</tr>
					<tr>
						<td>About Me</td>
						<td><?php echo $user_details['aboutme']; ?></td>
					</tr>
					<tr>
						<td style="vertical-align: top;">Interests</td>
						<td>
							<ul>
								<?php
									foreach ($user_details['interests'] as $key => $value) {
										echo "<li>".$value."</li>";
									}
								?>
							</ul>
						</td>
					</tr>
				</table>
			</div>
			<?php
				} else {
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
				}
			?>
		</div>
	</div>
</body>
</html>