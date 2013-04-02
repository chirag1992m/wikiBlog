<?php
ob_start();
	include_once('includes/session.php');
	
	if($session->loggedin) {
		header("location: profile.php?username=".$session->username);
	}
ob_flush();
include_once('entities/article.php');
include_once('entities/blogpost.php');
$article = new Article($user->getDatabaseClient());
$post = new BlogPost($user->getDatabaseClient());
$all_articles = $article->getLatestArticles(3);
$all_blogs = $post->getLatestPosts(3);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>WikiBlog | HOME</title>
	 
	<link href="css/reset.css" rel="stylesheet" type="text/css" />
	<link href="css/main.css" rel="stylesheet" type="text/css" />
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
					echo $session->username;
				?>
			</div>
		</div>
	</header>
	<div id="content">
		<div class="wrapper">
			<div id="wikiblog_about">
				<!-- It is a blog and article writing/viewing web-application for users.<br/><br/>
				Its features include:<br/>
				<ul>
					<li>Every writer will have a separate user account identified by a unique username.</li>
					<li>A user has to authenticate him/her-self using a password set by the user himself.</li>
					<li>Every user will have its own profile (personal details + profile image).</li>
					<li>One user can follow another to get his updates.</li>
					<li>A user can view the blogs and articles without having a user account.</li>
					<li>Every user has the option to write a personal blog or an article for the general community.</li>
					<li>A blog post has only one version and it belongs to a particular user.</li>
					<li>An article is anonymous and can be edited or re-written by any user.</li>
					<li>Every version of the article will be stored differentiated from one-another using a version number and its timestamp.</li>
					<li>One can search for other users, articles or even blogs.</li>
					<li>For the search engine to work,
						<ul>
						<li>Blog/article has a set of tags.</li>
						<li>Blog/article contains keywords.</li>
						<li>An article will contain references.</li>
						<li>User has a set of interests, a username.</li>
						<li>All these will be indexed in the database using a secondary index in a B-Tree structure.</li>
						</ul>
					</li>
					<li>A user can rate an article out of 5.</li>
				</ul> -->
				<h1 style="font-size:150%; font-weight: 900;">Newly Added Articles:</h1>
				<div id="writes_list">
					<?php
						if(is_null($all_articles)) {
					?>
					<div class="error big">
						Sorry, No new articles found.
					</div>
					<?php
						} else {
					?>
					<?php
					foreach ($all_articles as $key => $value) {
					?>
						<div>
							<h1><a href="article?id=<?php echo $value['id']; ?>"><?php echo $value['name']; ?></a><span style="font-size: 60%; color: #666; vertical-align: bottom;">&nbsp;&nbsp;&nbsp; Written at: <?php echo $value['time']; ?></span></h1>
							<div style="text-align: right;">
								<?php
									echo substr(strip_tags($value['text']), 0, 100)."...";
								?>
							</div>
						</div>
					<?php
					}
				?>
					<?php
						}
					?>
				</div>
				<h1 style="font-size:150%; font-weight: 900;">Newly Added blog Posts:</h1>
				<div id="writes_list">
					<?php
						if(is_null($all_blogs)) {
					?>
					<div class="error big">
						Sorry, No new blog posts found.
					</div>
					<?php
						} else {
					?>
					<?php
					foreach ($all_blogs as $key => $value) {
					?>
						<div style="overflow: hidden;">
							<h1><a href="post?id=<?php echo $value['id']; ?>"><?php echo $value['name']; ?></a><span style="font-size: 60%; color: #666; vertical-align: bottom;">&nbsp;&nbsp;&nbsp; Written at: <?php echo $value['time']; ?></span></h1>
							<div style="text-align: right;">
								<?php
									echo substr(strip_tags($value['text']), 0, 100)."...";
								?>
							</div>
						</div>
					<?php
					}
				?>
					<?php
						}
					?>
				</div>
			</div>
			<div id="login_box" style="vertical-align: top;">
				<?php
					if(isset($_SESSION['success_registration'])) {
						if($_SESSION['success_registration'] == true) {
							unset($_SESSION['success_registration']);
				?>
					<div class="success">
						<?php
							echo $_SESSION['success_registration_message'];
							unset($_SESSION['success_registration_message']);
						?>
					</div>
				<?php
						}
					}
				?>
				<?php
					if(isset($_SESSION['success_login'])) {
						unset($_SESSION['success_login']);
						$success = false;
				?>
					<div class="error">
						<?php
							echo $form->GetError("login");
						?>
					</div>
				<?php
					}
				?>
				<form action="process.php" method="POST">
					<div><h1>LOGIN</h1></div>
					<div class="clear"></div>
					<div>
						<label for="username">Username</label><span class="error small">
						<?php
							if(isset($success) && !$success) {
								echo $form->GetError("username");
							}
						?>
						</span><br/>
						<input type="text" name="username" autofocus="autofocus" value=<?php echo "\"".$form->GetValue("username")."\""; ?>>
					</div>
					<div>
						<label for="password">Password</label><span class="error small">
						<?php
							if(isset($success) && !$success) {
								echo $form->GetError("password");
							}
						?>
						</span><br/>
						<input type="password" name="pass">
					</div>
					<div>
						<input type="submit" value="Login">
						<h1><a href="registration.php">register</a></h1>
					</div>
					<input type="hidden" name="type" value="2">
				</form>
			</div>
		</div>
	</div>
</body>
</html>