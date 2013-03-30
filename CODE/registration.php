<?php
ob_start();
	include_once('includes/session.php');
	
	if($session->loggedin) {
		header("location: profile.php?username=".$session->username);
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
					echo $session->username;
				?>
			</div>
		</div>
	</header>
	<div id="content">
		<div class="wrapper">
			<form action="process.php" method="POST" enctype="multipart/form-data">
				<?php
					//var_dump($_SESSION);
					if(isset($_SESSION['success_registration'])) {
						if($_SESSION['success_registration'] == true) {
							$success = true;
				?>
					<div class="success">
						<?php
							echo $_SESSION['success_registration_message'];
							unset($_SESSION['success_registration_message']);
						?>
					</div>
				<?php
						} else {
							$success = false;
				?>
					<div class="error">
						Sorry, registration was unsuccessful.
					</div>
				<?php
						}
						unset($_SESSION['success_registration']);
					}
					//var_dump($form);
				?>
				<div id="reg_form">
					<table>
						<tr>
							<td>First Name<br/><span>Within 30 characters</span></td>
							<td>
								<?php
									$field = "firstname";
									if(isset($success) && !$success) {
										if($error = $form->GetError($field)) {
											echo "<div class=\"error small\">".$error."</div>";
										}
									}
									$value = $form->GetValue($field);
								?>
								<input autofocus="autofocus" type="text" name="firstname" placeholder="Enter your first name here" required="required" value="<?php echo $value; ?>">
							</td>
						</tr>
						<tr>
							<td>Last Name<br/><span>Within 30 characters</span></td>
							<td>
								<?php
									$field = "lastname";
									if(isset($success) && !$success) {
										if($error = $form->GetError($field)) {
											echo "<div class=\"error small\">".$error."</div>";
										}
									}
									$value = $form->GetValue($field);
								?>
								<input type="text" name="lastname" placeholder="Enter your last name here" value="<?php echo $value; ?>"></td>
						</tr>
						<tr>
							<td>Username<br/><span>Within 30 characters</span></td>
							<td>
								<?php
									$field = "username";
									if(isset($success) && !$success) {
										if($error = $form->GetError($field)) {
											echo "<div class=\"error small\">".$error."</div>";
										}
									}
									$value = $form->GetValue($field);
								?>
								<input type="text" name="username" placeholder="Enter your display name" required="required" value="<?php echo $value; ?>"></td>
						</tr>
						<tr>
							<td>Password<br/><span>Within 30 characters</span></td>
							<td style="vertical-align: bottom;">
								<?php
									$field = "password";
									if(isset($success) && !$success) {
										if($error = $form->GetError($field)) {
											echo "<div class=\"error small\">".$error."</div>";
										}
									}
									$value = $form->GetValue($field);
								?>
								<input type="password" name="password" placeholder="Enter your password here" required="required" value="<?php echo $value; ?>"></td>
						</tr>
						<tr>
							<td>Email-Id<br/><span>Within 30 characters</span></td>
							<td>
								<?php
									$field = "emailid";
									if(isset($success) && !$success) {
										if($error = $form->GetError($field)) {
											echo "<div class=\"error small\">".$error."</div>";
										}
									}
									$value = $form->GetValue($field);
								?>
								<input type="email" name="emailid" placeholder="Enter your email-ID" required="required" value="<?php echo $value; ?>"></td>
						</tr>
						<tr>
							<td>Profile Picture<br/><span>Max 1-MB (only JPEG, PNG, JPG)</span></td>
							<td>
								<?php
									$field = "profilepic";
									if(isset($success) && !$success) {
										if($error = $form->GetError($field)) {
											echo "<div class=\"error small\">".$error."</div>";
										}
									}
									$value = $form->GetValue($field);
								?>
								<input type="file" name="profilepic"></td>
						</tr>
						<tr>
							<td>About You</td>
							<td>
								<?php
									$field = "aboutme";
									if(isset($success) && !$success) {
										if($error = $form->GetError($field)) {
											echo "<div class=\"error small\">".$error."</div>";
										}
									}
									$value = $form->GetValue($field);
								?>
								<textarea name="aboutme" rows="10" placeholder="Tell us something about yourself!"><?php echo $value; ?></textarea></td>
						</tr>
						<tr>
							<td>Interests<br/><span>Separate your interests by commas(,)</span></td>
							<td>
								<?php
									$field = "interests";
									if(isset($success) && !$success) {
										if($error = $form->GetError($field)) {
											echo "<div class=\"error small\">".$error."</div>";
										}
									}
									$value = $form->GetValue($field);
								?>
								<input type="text" name="interests" placeholder="interest1, interest2, ..." value="<?php echo $value; ?>"></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" value="Register" name="submit"></td>
						</tr>
					</table>
					<input type="hidden" name="type" value="1">
				</div>
			</form>
		</div>
	</div>
</body>
</html>