<?php
chdir(dirname('__FILE__'));
	include_once('../includes/session.php');
	if($session->loggedin) {
		include_once('../entities/likes.php');

		$likes = new Likes($user->getDatabaseClient());

		if(isset($_GET['user']) && isset($_GET['post']) && isset($_GET['task'])) {
			$user = intval($_GET['user']);
			$post = intval($_GET['post']);

			if($user == $session->userid) {
				switch ($_GET['task']) {
					case 'like_post':
						if($likes->addChoice($post, $user, 1)) {
							$counts = $likes->getLikesDislikesCount($post);
							echo json_encode(array('success' => true, 'type' => 'like', 'likes' => $counts['like'], 'dislikes' => $counts['dislike']));
						} else {
							echo "Error: some error in processing your request.";
						}
						break;

					case 'unlike_post':
						if($likes->addChoice($post, $user, 0)) {
							$counts = $likes->getLikesDislikesCount($post);
							echo json_encode(array('success' => true, 'type' => 'dislike', 'likes' => $counts['like'], 'dislikes' => $counts['dislike']));
						} else {
							echo "Error: some error in processing your request.";
						}
						break;

					case 'remove_like':
					case 'remove_unlike':
						$likes->removeChoice($post, $user);
						$counts = $likes->getLikesDislikesCount($post);
						echo json_encode(array('success' => true, 'type' => 'remove', 'likes' => $counts['like'], 'dislikes' => $counts['dislike']));
						break;

					default:
						echo "Error: Wrong task given.";
						break;
				}
			} else {
				echo "Error: Request from wrong user.";
			}
		} else {
			echo "Error: Not enough arguments";
		}
	} else {
		echo "Error: No user not logged in";
	}
?>