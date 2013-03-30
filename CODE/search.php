<?php
ob_start();
include_once('includes/session.php');

if(isset($_GET['query'])) {
	$query_string = $_GET['query'];
	if(isset($_GET['type'])) {
		switch ($_GET['type']) {
			case 'article':
				$type = 1;
				break;
			
			case 'post':
				$type = 2;
				break;

			case 'user':
				$type = 3;
				break;

			default:
				$type = null;
				header("location: search?query=".urlencode($query_string)."&type=article");			
				break;
		}
	} else {
		header("location: search?query=".urlencode($query_string)."&type=article");
	}
} else {
	header("location: /");
}

include_once('entities/article.php');
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
	<link href="css/starify.css" rel="stylesheet" type="text/css" />
	<link href="css/main.css" rel="stylesheet" type="text/css" />

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
		<div style="position: fixed; width: 150px; right: 10px; top: 100px; border-left: 1px solid #777; padding: 10px;">
			<ul>
				<li style="margin: 10px;"><a href="search?query=<?php echo urlencode($query_string); ?>&amp;type=article">Articles</a></li>
				<li style="margin: 10px;"><a href="search?query=<?php echo urlencode($query_string); ?>&amp;type=post">Blog Posts</a></li>
				<li style="margin: 10px;"><a href="search?query=<?php echo urlencode($query_string); ?>&amp;type=user">Users</a></li>
			</ul>
		</div>
		<div style="width: 800px;">
	<?php
		$keyword_count = $commonfunctions->keyword_extraction($query_string);
	?>
	<div style="margin: 10px;">
		<h1 style="text-align: center; font-size: 125%; font-weight: 900;">Your seach query: <?php echo $query_string; ?></h1>
	</div>
	<?php
		if($type == 1) {
			$article = new Article($user->getDatabaseClient());
			$articles = $article->searchArticles($keyword_count);
			if(count($articles) == 0) {
		?>
			<div class="error big">
				No search Results for your query in articles.
			</div>
		<?php
			} else {
		?>
			<div id="writes_list">
		<?php
				foreach ($articles as $key => $value) {
					$article_data = $article->getLatestArticleDataByID($value);
					$article_version = $article->getLatestVersionNumber($value);

	?>
					<div>
							<h1><a href="article?id=<?php echo $value; ?>&amp;version=latest"><?php echo $article_data['articleName']; ?></a> (version: <?php echo $article_version; ?>)&nbsp;&nbsp;&nbsp;<span style="font-size: 60%; color: #666; vertical-align: bottom;">Written at: <?php echo $article_data['lastModified']; ?></span></h1>
							<div>
								Article Rating: <div class="stars" data-type="normal" data-rating="<?php echo $article->getAverageRating($value, $article_version); ?>"></div>
							</div>
						</div>
	<?php
				}
		?>
			</div>
		<?php
			}
		} else if($type == 2) {
			$post = new BlogPost($user->getDatabaseClient());
			$posts = $post->searchPost($keyword_count);
			
			if(count($posts) == 0) {
		?>
			<div class="error big">
				No search Results for your query in Blog Posts.
			</div>
		<?php
			} else {
				$likes = new Likes($user->getDatabaseClient());
				$comments = new Comments($user->getDatabaseClient());
			?>
				<div id="writes_list">
			<?php
				foreach ($posts as $key => $value) {
					$post_data = $post->getPostGivenPostid($value);
	?>
					<div>
						<h1><a href="post?id=<?php echo $value; ?>"><?php echo $post_data['name']; ?></a>&nbsp;&nbsp;&nbsp;<span style="font-size: 60%; color: #666; vertical-align: bottom;">Written at: <?php echo $post_data['time']; ?></span></h1>
						<div>
							<?php
								$likecount = $likes->getLikesDislikesCount($value);
								$commentcount = $comments->getCommentCount($value);
							?>
							<a href="users_list?type=like&amp;postid=<?php echo $value; ?>" target="_blank"><span><img src="img/like.png" alt="Likes" width="20" /> <?php echo $likecount['like']; ?></span></a>&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="users_list?type=dislike&amp;postid=<?php echo $value; ?>" target="_blank"><span><img src="img/dislike.png" alt="Dislikes" width="20" /> <?php echo $likecount['dislike']; ?></span></a>&nbsp;&nbsp;&nbsp;&nbsp;
							<span><img src="img/comment.png" alt="Comments" width="20" /> <?php echo $commentcount; ?></span>
						</div>
						<div>
							<?php
								foreach ($post_data['tags'] as $key2 => $value2) {
									echo "<a href=\"search?query=".urlencode($value2)."\">".$value2."</a> ";
								}
							?>
						</div>
						<div style="text-align: right;">
							<?php
								echo substr(strip_tags($post_data['text']), 0, 300)."...";
							?>
						</div>
					</div>
	<?php
				}
			?>
				</div>
			<?php
			}
		} else if($type == 3) {
			$users = $user->searchUsers($keyword_count);
			if(count($users) == 0) {
		?>
			<div class="error big">
				No search Results for your query in Users.
			</div>
		<?php
			} else {
		?>
				<div id="users_list">
		<?php
				foreach ($users as $key => $value) {
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
	<script type="text/javascript">
		$(document).ready(function() {
			$('.stars').starify();
		});
	</script>
</body>
</html>