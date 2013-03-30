<?php
ob_start();
	include_once('includes/session.php');

chdir(dirname(__FILE__));
	include_once('entities/blogpost.php');
	include_once('entities/comment.php');
	include_once('entities/likes.php');
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
		<?php
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
					echo $username." posts: (Total: ";
					$blogs = new BlogPost($user->getDatabaseClient());
					$likes = new Likes($user->getDatabaseClient());
					$comments = new Comments($user->getDatabaseClient());

					$count = $blogs->getPostCount($userid);
					echo $count.")";
					
					$all_blogs = $blogs->getPostsGivenUser($userid);
					// var_dump($all_blogs);
				?>
			</div>
			<div id="writes_list">
				<?php
					foreach ($all_blogs as $key => $value) {
					?>
					<div>
						<h1><a href="post?id=<?php echo $value['postid']; ?>"><?php echo $value['name']; ?></a>&nbsp;&nbsp;&nbsp;<span style="font-size: 60%; color: #666; vertical-align: bottom;">Written at: <?php echo $value['time']; ?></span></h1>
						<div>
							<?php
								$likecount = $likes->getLikesDislikesCount($value['postid']);
								$commentcount = $comments->getCommentCount($value['postid']);
							?>
							<a href="users_list?type=like&amp;postid=<?php echo $value['postid']; ?>" target="_blank"><span><img src="img/like.png" alt="Likes" width="20" /> <?php echo $likecount['like']; ?></span></a>&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="users_list?type=dislike&amp;postid=<?php echo $value['postid']; ?>" target="_blank"><span><img src="img/dislike.png" alt="Dislikes" width="20" /> <?php echo $likecount['dislike']; ?></span></a>&nbsp;&nbsp;&nbsp;&nbsp;
							<span><img src="img/comment.png" alt="Comments" width="20" /> <?php echo $commentcount; ?></span>
						</div>
						<div>
							<?php
								foreach ($value['tags'] as $key2 => $value2) {
									echo "<a href=\"search?query=".urlencode($value2)."\">".$value2."</a> ";
								}
							?>
						</div>
						<div style="text-align: right;">
							<?php
								echo substr(strip_tags($value['text']), 0, 300)."...";
							?>
						</div>
					</div>
					<?php
					}
				?>
			</div>
		<?php
			}
		?>
	</div>
</body>
</html>