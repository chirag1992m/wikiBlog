<?php
ob_start();
	include_once('includes/session.php');

chdir(dirname(__FILE__));
	include_once('entities/article.php');
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
			$article = new Article($user->getDatabaseClient());
			if(isset($_GET['id'])) {
				$not_enough_arguments = false;
				$articleid = intval($_GET['id']);

				$article_main_data = $article->getLatestArticleDataByID($articleid);
				if($article_main_data != null) {
					if(isset($_GET['version']) && $_GET['version'] != "latest") {
						$version = intval($_GET['version']);

						$article_version_data = $article->getArticleDataVersion($articleid, $version);
						if($article_version_data == null) {
							header("location: article?id=".$articleid."&version=latest");
						}
					} else {
						$version = $article->getLatestVersionNumber($articleid);
						header("location: article?id=".$articleid."&version=".$version);
					}
				}
			} else {
				$not_enough_arguments = true;
			}
			ob_flush();
		?>
		<?php
			if($not_enough_arguments) {
		?>
			<div class="error big">
				Sorry, the given URL is incorrect.
			</div>
		<?php
			} else {
				if(is_null($article_main_data)) {
			?>
				<div class="error big">
					Sorry, No post with article-ID <?php echo $articleid; ?> was found.
				</div>
			<?php
				} else {
					$user_details = $user->getAllDetailsByUserid($article_version_data['uid']);
			?>
			<div id="article">
				<?php
					if($session->userid != -1) {
				?>
					<div><a href="write?type=article&amp;articleid=<?php echo $articleid; ?>"><button>Edit Article</button></a></div>
				<?php
					}
				?>
				<h1><?php echo $article_main_data['articleName']; ?> (Version: <?php echo $version; ?>)</h1>
				<div class="writer">
					<?php 
						echo ($version == 1 ? 'Written by' : 'Edited by').": <a href=\"profile?userid=".$article_version_data['uid']."\">".$user_details['firstname']." ".$user_details['lastname']."</a>";
						echo "<br/>";
						echo "Written at: ".$article_version_data['time'];
					?>
				</div>
				<div class="text">
					<?php echo $article_version_data['text']; ?>
				</div>
				<div style="text-align: right;">
					<?php
						$tags = $article->getAllTags($articleid);
						foreach ($tags as $key2 => $value2) {
							if(is_null($value2[1]))
								echo "<a href=\"search?query=".urlencode($value2[0])."\">".$value2[0]."</a> ";
							else
								echo "<a target=\"_blank\" href=\"".$value2[1]."\">".$value2[0]."</a> ";
						}
					?>
				</div>
				<div class="ratings">
					<?php
						$average_rating = $article->getAverageRating($articleid, $version);
						$user_rating = null;
						if($session->userid != -1) {
							$user_rating = $article->getUserRating($session->userid, $articleid, $version);
						}
					?>
					Article Rating: <div class="stars" data-type="normal" data-rating="<?php echo $average_rating; ?>"></div>
					<div>Your Rating:
						<?php
							if(is_null($user_rating) && $session->userid == -1)
								echo "Login to rate.";
							else if(is_null($user_rating)) {
						?>
								<form action="relations/rating.php" method="POST">
									<select name="rating">
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
									</select>
									<input type="hidden" name="article" value="<?php echo $articleid; ?>">
									<input type="hidden" name="version" value="<?php echo $version; ?>">
									<input type="submit" name="submit" value="submit">
								</form>
						<?php
							}
							else {
						?>
							<div class="stars" data-type="normal" data-rating="<?php echo $user_rating; ?>"></div>
						<?php
							}
						?>
					</div>
				</div>
				<div class="version_summary">
					<span>Version Summary: </span>
					<?php
						$versions = $article->getAllVersions($articleid);
					?>
					<ul>
						<?php
							foreach ($versions as $key => $value) {
						?>
							<li>
								Version: <a href="article?id=<?php echo $articleid; ?>&amp;version=<?php echo $value['version']; ?>"><?php echo $value['version']; ?></a> written by user with USERID <a href="profile?userid=<?php echo $value['uid']; ?>"><?php echo $value['uid']; ?></a> at <?php echo $value['time']; ?>
							</li>
						<?php
							}
						?>
					</ul>
				</div>
			</div>
		<?php
				}
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