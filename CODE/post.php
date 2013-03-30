<?php
ob_start();
	include_once('includes/session.php');

chdir(dirname(__FILE__));
	include_once('entities/blogpost.php');
	include_once('entities/likes.php');
	include_once('entities/comment.php');
ob_flush();

function printChildren($children) {
	global $user, $commonfunctions, $postid;
	if(!is_null($children)) {
		echo "<ul>";
		foreach ($children as $commentkey => $commentvalue) {
			echo "<li data-comment=".$commentvalue->id.">";
				$user_details = $user->getAllDetailsByUserid($commentvalue->userid);
				echo "<img src=".$commonfunctions->getProfilePath($user_details['profilepic'], $user_details['username'])." alt=\"profile_pic\" />";
				echo "<a href=profile?userid=".$commentvalue->userid.">".$user_details['firstname']." ".$user_details['lastname']."</a><br/>";
				echo "<span>written at: ".$commentvalue->time."</span>";
				echo "<div class=clear></div>";
				echo "<div class=comment_text>".$commentvalue->comment."</div>";
				echo "<a onclick=toggleReply(".$commentvalue->id.")>Reply</a>";
				echo "<form id=reply_".$commentvalue->id." class=reply action=\"relations/comment.php?postid=".$postid."\" method=\"POST\">&nbsp; &nbsp; &nbsp;<input type=\"text\" name=\"comment\" required><input type=\"submit\" value=\"Reply\" name=submit><input type=hidden value=".$commentvalue->id." name=commentid></form>";

				if(!is_null($commentvalue->children)) {
					printChildren($commentvalue->children);
				}
			echo "</li>";
		}
		echo "</ul>";
	}
}
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
			if(isset($_GET['id'])) {
				$postid = intval($_GET['id']);
				$not_enough_arguments = false;
			} else {
				$not_enough_arguments = true;
			}
		?>
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
					$user_details = $user->getAllDetailsByUserid($post_details['uid']);
					$likes = new Likes($user->getDatabaseClient());
					$comments = new Comments($user->getDatabaseClient());
				?>
					<div id="post">
						<h1><?php echo $post_details['name']; ?></h1>
						<div class="writer">
							<?php 
								echo "Written by: <a href=\"profile?userid=".$post_details['uid']."\">".$user_details['firstname']." ".$user_details['lastname']."</a>";
								echo "<br/>";
								echo "Written at: ".$post_details['time'];
							?>
						</div>
						<div class="text">
							<?php echo $post_details['text']; ?>
						</div>
						<div style="text-align: right;">
							<?php
								foreach ($post_details['tags'] as $key2 => $value2) {
									echo "<a href=\"search?query=".urlencode($value2)."\">".$value2."</a> ";
								}
							?>
						</div>
						<div class="like_summary">
							<?php
								$likecount = $likes->getLikesDislikesCount($postid);
								$commentcount = $comments->getCommentCount($postid);
							?>
							<div><a href="users_list?type=like&amp;postid=<?php echo $postid; ?>" target="_blank"><img src="img/like.png" alt="Likes" width="20" /> <span id="like_count"><?php echo $likecount['like']; ?></span></a></div>
							<div><a href="users_list?type=dislike&amp;postid=<?php echo $postid; ?>" target="_blank"><img src="img/dislike.png" alt="Dislikes" width="20" /> <span id="dislike_count"><?php echo $likecount['dislike']; ?></span></a></div>
							<div><span><img src="img/comment.png" alt="Comments" width="20" /> <?php echo $commentcount; ?></span></div>
						</div>
						<div id="like_button">
						<?php
							$like_exist = $likes->checkLikeExistence($postid, $session->userid);
							if($like_exist == -1) {
						?>
							<div><button id="like_post" data-post="<?php echo $postid; ?>" data-user="<?php echo $session->userid; ?>">Like Post</button></div>
							<div><button id="unlike_post" data-post="<?php echo $postid; ?>" data-user="<?php echo $session->userid; ?>">Unlike Post</button></div>
						<?php
							} else if($like_exist == 1) {
						?>
							<div><button id="remove_like" data-post="<?php echo $postid; ?>" data-user="<?php echo $session->userid; ?>">Remove Like</button></div>
							<div><button id="unlike_post" data-post="<?php echo $postid; ?>" data-user="<?php echo $session->userid; ?>">Unlike Post</button></div>
						<?php
							} else {
						?>
							<div><button id="like_post" data-post="<?php echo $postid; ?>" data-user="<?php echo $session->userid; ?>">Like Post</button></div>
							<div><button id="remove_unlike" data-post="<?php echo $postid; ?>" data-user="<?php echo $session->userid; ?>">Remove Unlike</button></div>
						<?php
							}
						?>
						</div>
						<div class="comments">
						<form action="relations/comment.php?postid=<?php echo $postid; ?>" method="POST">
							Add Comment: &nbsp; &nbsp; &nbsp;<input type="text" name="comment" required>
							<input type="submit" value="Comment" name=submit>
						</form>
						<?php
							$comment_tree = $comments->getCommentTree($postid);
							// var_dump($comment_tree);
							echo "<ul>";
							foreach ($comment_tree as $commentkey => $commentvalue) {
								echo "<li data-comment=".$commentvalue->id.">";
								$user_details = $user->getAllDetailsByUserid($commentvalue->userid);
								echo "<img src=".$commonfunctions->getProfilePath($user_details['profilepic'], $user_details['username'])." alt=\"profile_pic\" />";
								echo "<a href=profile?userid=".$commentvalue->userid.">".$user_details['firstname']." ".$user_details['lastname']."</a><br/>";
								echo "<span>written at: ".$commentvalue->time."</span>";
								echo "<div class=clear></div>";
								echo "<div class=comment_text>".$commentvalue->comment."</div>";
								echo "<a onclick=toggleReply(".$commentvalue->id.")>Reply</a>";
								echo "<form id=reply_".$commentvalue->id." class=reply action=\"relations/comment.php?postid=".$postid."\" method=\"POST\">&nbsp; &nbsp; &nbsp;<input type=\"text\" name=\"comment\" required><input type=\"submit\" value=\"Reply\" name=submit><input type=hidden value=".$commentvalue->id." name=commentid></form>";
								if(!is_null($commentvalue->children)) {
									printChildren($commentvalue->children);
								}
								echo "</li>";
							}
							echo "</ul>";
						?>
						</div>
					</div>
				<?php
				}
			}
		?>
	</div>
</body>
</html>
