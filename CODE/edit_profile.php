<?php
ob_start();
	include_once('includes/session.php');
	
	if(!$session->loggedin) {
		header("location: index.php");
	}
ob_flush();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>WikiBlog | Register</title>
	 
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
					<input type="submit" value="Search" />
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
	<div id="content">
		<div class="wrapper">
			<form action="process.php" method="POST" enctype="multipart/form-data">
				<div id="reg_form">
					<?php
						$userid = $session->userid;

						$user_details = $user->getAllDetailsByUserid($userid);
					?>
					<table>
						<tr>
							<td>First Name<br/><span>Within 30 characters</span></td>
							<td>
								<?php
									$field = "firstname";
									if($error = $form->GetError($field)) {
										echo "<div class=\"error small\">".$error."</div>";
									}
								?>
								<input autofocus="autofocus" type="text" name="firstname" placeholder="Enter your first name here" required="required" value="<?php echo $user_details['firstname'] ?>">
							</td>
						</tr>
						<tr>
							<td>Last Name<br/><span>Within 30 characters</span></td>
							<td>
								<?php
									$field = "lastname";
									if($error = $form->GetError($field)) {
										echo "<div class=\"error small\">".$error."</div>";
									}
								?>
								<input type="text" name="lastname" placeholder="Enter your last name here" value="<?php echo $user_details['lastname'] ?>">
							</td>
						</tr>
						<tr>
							<td>Username<br/><span>Within 30 characters</span></td>
							<td>
								<input type="text" readonly name="username" placeholder="Enter your display name" required="required" value="<?php echo $user_details['username'] ?>">
							</td>
						</tr>
						<tr>
							<td>Old Password</td>
							<td style="vertical-align: bottom;">
								<?php
									$field = "oldpass";
									if($error = $form->GetError($field)) {
										echo "<div class=\"error small\">".$error."</div>";
									}
								?>
								<input type="password" name="oldpass" placeholder="Enter your old password here" value="">
							</td>
						</tr>
						<tr>
							<td>New Password<br/><span>Within 30 characters</span></td>
							<td style="vertical-align: bottom;">
								<?php
									$field = "newpass";
									if($error = $form->GetError($field)) {
										echo "<div class=\"error small\">".$error."</div>";
									}
								?>
								<input type="password" name="newpass" placeholder="Enter your new password here" value="">
							</td>
						</tr>
						<tr>
							<td>Email-Id<br/><span>Within 30 characters</span></td>
							<td>
								<?php
									$field = "emailid";
									if($error = $form->GetError($field)) {
										echo "<div class=\"error small\">".$error."</div>";
									}
								?>
								<input type="email" name="emailid" placeholder="Enter your email-ID" required="required" value="<?php echo $user_details['emailid'] ?>">
							</td>
						</tr>
						<tr>
							<td style="vertical-align: top;">Profile Picture<br/><span>Max 1-MB (only JPEG, PNG, JPG)</span></td>
							<td>
								<?php
									$field = "profilepic";
									if($error = $form->GetError($field)) {
										echo "<div class=\"error small\">".$error."</div>";
									}
								?>
								<input type="file" name="profilepic"><br/>
								<img src=
									<?php
										echo $commonfunctions->getProfilePath($user_details['profilepic'], $session->username);
									?>
									alt="profile_pic" width="100" height="100" class="thumbnail_pic"
								/>
							</td>
						</tr>
						<tr>
							<td>About You</td>
							<td>
								<?php
									$field = "aboutme";
									if($error = $form->GetError($field)) {
										echo "<div class=\"error small\">".$error."</div>";
									}
								?>
								<textarea name="aboutme" rows="10" placeholder="Tell us something about yourself!"><?php echo $user_details['aboutme'] ?></textarea>
							</td>
						</tr>
						<tr>
							<td>Interests<br/><span>Separate your interests by commas(,)</span></td>
							<td>
								<?php
									$field = "interests";
									if($error = $form->GetError($field)) {
										echo "<div class=\"error small\">".$error."</div>";
									}
								?>
								<input type="text" name="interests" placeholder="interest1, interest2, ..." value="<?php foreach ($user_details['interests'] as $key => $value) {
									echo $value.", ";
								} ?>">
							</td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" value="Save Changes" name="submit"></td>
						</tr>
					</table>
					<input type="hidden" name="type" value="3">
				</div>
			</form>
		</div>
	</div>
</body>
</html>