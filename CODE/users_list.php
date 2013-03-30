<?php
ob_start();
	include_once('includes/session.php');

chdir(dirname(__FILE__));
	include_once('entities/blogpost.php');
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
			$not_enough_arguments = true;

			if(isset($_GET['type'])) {
				switch ($_GET['type']) {
					case 'like':
						$type = 1;
						break;

					case 'dislike':
						$type = 2;
						break;
					
					default:
						$not_enough_arguments = true;
						break;
				}
			} else {
				$not_enough_arguments = true;
			}

			if(isset($type) && ($type == 1 || $type == 2) && isset($_GET['postid'])) {
				$not_enough_arguments = false;
				$postid = intval($_GET['postid']);
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
				$posts = new BlogPost($user->getDatabaseClient());
				$post_details = $posts->getPostGivenPostid($postid);

				if(is_null($post_details)) {
				?>
					<div class="error big">
						Sorry, No post with post-ID <?php echo $postid; ?> was found.
					</div>
				<?php
				} else {
				?>
					<div class="big">
						<?php
							$likes = new Likes($user->getDatabaseClient());
							if($type == 1) {
								$data = $likes->getLikeUsers($postid);
							} else {
								$data = $likes->getDislikeUsers($postid);
							}
							echo "Users ".($type == 1 ? 'liking' : 'disliking')." the post \"".$post_details['name']."\" are: (Total: ".count($data).")";
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
											echo substr($users_data['aboutme'], 0, 100)."....";
										?><br/><br/>
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