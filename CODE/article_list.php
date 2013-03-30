<?php
ob_start();
	include_once('includes/session.php');

chdir(dirname(__FILE__));
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
	<link href="css/starify.css" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/starify.js"></script>
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
					echo $username." articles: (Total: ";
					$articles = new Article($user->getDatabaseClient());
					
					$count = $articles->getAllArticleCount($userid);
					echo $count.")";
					
					$all_articles = $articles->getArticleListByUser($userid);
					// var_dump($all_articles);
				?>
			</div>
			<div id="writes_list">
				<?php
					foreach ($all_articles as $key => $value) {
					?>
						<div>
							<h1><a href="article?id=<?php echo $value['articleid']; ?>&amp;version=<?php echo $value['version']; ?>"><?php echo $value['name']; ?></a> (version: <?php echo $value['version']; ?>)&nbsp;&nbsp;&nbsp;<span style="font-size: 60%; color: #666; vertical-align: bottom;">Written at: <?php echo $value['time']; ?></span></h1>
							<div>
								Article Rating: <div class="stars" data-type="normal" data-rating="<?php echo $value['avgrating']; ?>"></div>
								Your Rating:
									<?php
										if(is_null($value['rating']))
											echo "Not Yet Rated.";
										else {
									?>
									<div class="stars" data-type="normal" data-rating="<?php echo $value['rating']; ?>"></div>
									<?php
										}
									?>
							</div>
							<div>
								<?php
									$tags = "";
									foreach ($value['tags'] as $key2 => $value2) {
										if(is_null($value2[1]))
											echo "<a href=\"search?query=".urlencode($value2[0])."\">".$value2[0]."</a> ";
										else
											echo "<a target=\"_blank\" href=\"".$value2[1]."\">".$value2[0]."</a> ";
									}
								?>
							</div>
							<div style="text-align: right;">
								<?php
									echo substr(strip_tags($value['text']), 0, 300)."...";
								// var_dump($value);
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
	<script type="text/javascript">
		$(document).ready(function() {
			$('.stars').starify();
		});
	</script>
</body>
</html>