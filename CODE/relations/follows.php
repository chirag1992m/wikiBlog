<?php
chdir(dirname('__FILE__'));
	include_once('../includes/session.php');
	if($session->loggedin) {
		include_once('../entities/followers.php');

		$follows = new Follows(null);

		if(isset($_GET['user1']) && isset($_GET['user2']) && isset($_GET['task'])) {
			$user1 = intval($_GET['user1']);
			$user2 = intval($_GET['user2']);

			switch ($_GET['task']) {
				case 'remove':
					if(!$follows->removeFollows($user1, $user2)) {
						echo "Error: cannot remove following.";
					} else {
						$result = array();

						array_push($result, 1);
						array_push($result, true);
						array_push($result, $follows->getFollowersCount($user2));
						array_push($result, $follows->getFolloweesCount($user2));

						echo json_encode($result);
					}
					break;

				case 'add':
					if(!$follows->addNewFollower($user1, $user2)) {
						echo "Error: cannot add following.";
					} else {
						$result = array();

						array_push($result, 2);
						array_push($result, true);
						array_push($result, $follows->getFollowersCount($user2));
						array_push($result, $follows->getFolloweesCount($user2));

						echo json_encode($result);
					}
					break;
				
				default:
					echo "Error: Wrong task given.";
					break;
			}
		} else {
			echo "Error: Not enough arguments";
		}
	} else {
		echo "Error: No user not logged in";
	}
?>