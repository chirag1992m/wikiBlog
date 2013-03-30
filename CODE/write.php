<?php
ob_start();
	include_once('includes/session.php');
	if(!$session->loggedin) {
		header("location: index.php");
	}
chdir(dirname(__FILE__));
	include_once('entities/blogpost.php');
	include_once('entities/article.php');


if(isset($_GET['type'])) {
	switch ($_GET['type']) {
		case 'article':
			if(isset($_GET['articleid'])) {
				$type = 3;
				$articleid = intval($_GET['articleid']);
			} else {
				$type = 2;
			}
			break;
		
		case 'post':
		default:
			$type = 1;
			break;
	}
} else {
	$type = 1;
}
if($type == 3) {
	$article = new Article($user->getDatabaseClient());
	$article_data = $article->getLatestArticleDataByID($articleid);
	$articleTags = $article->getAllTags($articleid);

	if(is_null($article_data)) {
		header("location: write?type=article");
	}
}
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
	<script type="text/javascript" src="js/nicedit.js"></script>
	<script type="text/javascript" src="js/main.js"></script>
</head>

<body>
	<script type="text/javascript">
		bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
	</script>
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
		<form action="write_process.php" method="POST">
			<div id="reg_form">
				<?php
					if(isset($_SESSION['write_success'])) {
					?>
						<div class="<?php if($_SESSION['write_success']) echo 'success'; else echo 'error'; ?>">
							<?php echo $_SESSION['write_message']; ?>
						</div>
					<?php
						unset($_SESSION['write_success']);
						unset($_SESSION['write_message']);
					}
				?>
				<div class="error">
					<?php echo $form->GetError("form"); ?>
				</div>
				<table>
					<tr>
						<td>
							<?php
								if($type == 1)
									echo "Post";
								else
									echo "Article";
							?> Name<br/><span>Within 100 characters</span>
						</td>
						<td>
							<?php
								$value = $form->GetValue("name");
								if(strlen($value) == 0) {
									if($type == 1)
										$value = "New Post";
									else if($type == 2)
										$value = "New Article";
									else if($type == 3) {
										$value = $article_data['articleName'];
									}
								}
								if($error = $form->GetError("name")) {
									echo "<div class=\"error small\">".$error."</div>";
								}
							?>
							<input autofocus="autofocus" <?php if($type == 3) echo "readonly"; ?> type="text" name="name" placeholder="Name Here" required="required" value="<?php echo $value; ?>">
						</td>
					</tr>
					<tr>
						<td>Tags<br/><span>Separate the tags by commas(,)</span></td>
						<td>
							<?php
								$value = $form->GetValue("tags");
								if($error = $form->GetError("tags")) {
									echo "<div class=\"error small\">".$error."</div>";
								}
								if(strlen($value) == 0 && $type == 3) {
									$tags = "";
									foreach ($articleTags as $key => $value) {
										if(is_null($value[1]))
											$tags .= $value[0].", ";
									}
									$value = $tags;
								}
							?>
							<input type="text" name="tags" placeholder="tag1, tag2, ..." value="<?php echo $value; ?>">
						</td>
					</tr>
				<?php 
					if($type != 1) {
				?>
					<tr>
						<td>References<br/><span>Separate by commas (,)</span><br/><span>In the form of NAME=URL</span></td>
						<td>
							<?php
								$value = $form->GetValue("references");
								if($error = $form->GetError("references")) {
									echo "<div class=\"error small\">".$error."</div>";
								}
								if(strlen($value) == 0 && $type == 3) {
									$references = "";
									foreach ($articleTags as $key => $value) {
										if(!is_null($value[1]))
											$references .= $value[0]."=".$value[1].", ";
									}
									$value = $references;
								}
							?>
							<input type="text" name="references" placeholder="name1=URL1, name2=URL2, ..." value="<?php echo $value; ?>">
						</td>
					</tr>
				<?php
					}
				?>
					<tr>
						<td></td>
						<td><input type="submit" value="PUBLISH"></td>
					</tr>
				</table>
				<div>
					<?php
						if($type == 1)
							echo "<input type=hidden value=1 name=type />";
						else if($type == 2)
							echo "<input type=hidden value=2 name=type />";
						else if($type == 3) {
							echo "<input type=hidden value=3 name=type />";
							echo "<input type=hidden value=".$articleid." name=articleid />";
						}
					?>
					<div style="font-size: 150%; margin: 15px;" class="right">
						Enter Text Here
						<?php
							if($error = $form->GetError("text")) {
								echo "<div class=\"error small\">".$error."</div>";
							}
						?>
					</div>
					<div class="clear"></div>
					<div id="editor">
						<?php
							$value = $form->GetValue("text");
							if(strlen($value) == 0) {
								if($type == 1)
									$value = "Write Your blog Post HERE!";
								else if($type == 2)
									$value = "Write Your Article HERE!";
								else if($type == 3)
									$value = $article_data['articleText'];
							}
						?>
						<div style="background: #FFF;">
							<textarea name="text" id="text" style="width:100%; height:300px;"><?php echo $value;?></textarea>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</body>
</html>