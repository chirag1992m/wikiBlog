$(document).ready(function() {
	/* Adding Event Listeners */
	bindFollows();
	bindLikes();
	bindReplies();
});

function bindReplies() {
	$('.reply').hide();
}

function toggleReply(id) {
	$("#reply_"+id).slideToggle();
}


function bindFollows() {
	if($("#unfollow_user").length > 0) {
		$("#unfollow_user").on('click', unfollowUser);
	} else if ($("#follow_user").length > 0) {
		$("#follow_user").on('click', followUser);
	}
}
var follow_unfollow_request = null;
function followUnfollowCompleteRequest() {
	console.log("follow unfollow request state changed");
	if(follow_unfollow_request.readyState == 4 && follow_unfollow_request.status == 200) {
		if(follow_unfollow_request.responseText.indexOf("Error", 0) == -1) {
			var result = jQuery.parseJSON(follow_unfollow_request.responseText);
			
			if(result[1]) {
				// get the division for followers summary and change its values with the new ones
				$("#followers_summary").html(result[2]+"<br/>followers");
				$("#followees_summary").html("follows<br/>"+result[3]);

				switch(result[0]) {
					case 1:
						var ele = $("#unfollow_user");
						var parent = ele.parent();
						var user1 = ele.attr("data-follower");
						var user2 = ele.attr("data-followee");
						ele.remove();
						console.log(parent);
						ele = jQuery('<button/>', {
							"id": "follow_user",
							"data-follower": user1,
							"data-followee": user2
						}).appendTo(parent);
						ele.html("Follow User");
						break;

					case 2:
						var ele = $("#follow_user");
						var parent = ele.parent();
						var user1 = ele.attr("data-follower");
						var user2 = ele.attr("data-followee");
						ele.remove();
						console.log(parent);
						ele = jQuery('<button/>', {
							"id": "unfollow_user",
							"data-follower": user1,
							"data-followee": user2
						}).appendTo(parent);
						ele.html("Unfollow User");
						break;

					default: alert("Some error in processing your request, try again later.");
				}
				bindFollows();
			} else {
				alert("Some error in processing your request, try again later.");
			}
		} else {
			alert("Some error in processing your request, try again later.");
		}
		console.log(follow_unfollow_request.responseText);

		follow_unfollow_request = null;
	}
}
function unfollowUser() {
	/*alert($(this).attr('data-follower'));
	alert($(this).attr('data-followee'));*/

	var follower = $(this).attr('data-follower');
	var followee = $(this).attr('data-followee');
	
//console.log(randid);
	/*
	 * If there is already a request running, cancel it.
	 */
	if(follow_unfollow_request != null) {
		console.log('request in process.');
		return;
	}
	
	console.log('creating request object.');
	if(window.XMLHttpRequest) {
		follow_unfollow_request = new XMLHttpRequest();
	} else {
		follow_unfollow_request = new ActiveXObject("Microsoft.XMLHTTP");
	}
	follow_unfollow_request.onreadystatechange = followUnfollowCompleteRequest;
	follow_unfollow_request.open("GET", "relations/follows.php?user1="+follower+"&user2="+followee+"&task=remove");
	console.log('sending request.');
	follow_unfollow_request.send();
}
function followUser() {
	/*alert($(this).attr('data-follower'));
	alert($(this).attr('data-followee'));*/

	var follower = $(this).attr('data-follower');
	var followee = $(this).attr('data-followee');

	/*
	 * If there is already a request running, cancel it.
	 */
	if(follow_unfollow_request != null) {
		console.log('request in process.');
		return;
	}
	
	console.log('creating request object.');
	if(window.XMLHttpRequest) {
		follow_unfollow_request = new XMLHttpRequest();
	} else {
		follow_unfollow_request = new ActiveXObject("Microsoft.XMLHTTP");
	}
	follow_unfollow_request.onreadystatechange = followUnfollowCompleteRequest;
	follow_unfollow_request.open("GET", "relations/follows.php?user1="+follower+"&user2="+followee+"&task=add");
	console.log('sending request.');
	follow_unfollow_request.send();
}



var like_dislike_request = null;
function bindLikes() {
	$("#like_button").children("div").children("button").each(function() {
		$(this).on('click', likeDislikePost);
	});
}
function likeDislikeComplete() {
	console.log("like_dislike_request state changed");
	if(like_dislike_request.readyState == 4 && like_dislike_request.status == 200) {
		var response = like_dislike_request.responseText;
		if(response.indexOf("Error", 0) == -1) {
			response = jQuery.parseJSON(response);
			console.log(response);
			if(response.success == true) {
				var div1 = $("#like_button").children("div").first(),
					div2 = $("#like_button").children("div").last(),
					post = div1.children("button").attr('data-post'),
					user = div1.children("button").attr('data-user');
				$("#like_button").children("div").children("button").each(function() {
					$(this).remove();
				});
				
				switch(response.type) {
					case 'like':
						ele = jQuery('<button/>', {
							"id": "remove_like",
							"data-post": post,
							"data-user": user
						}).appendTo(div1);
						ele.html("Remove Like");
						ele = jQuery('<button/>', {
							"id": "unlike_post",
							"data-post": post,
							"data-user": user
						}).appendTo(div2);
						ele.html("Unlike Post");
						break;

					case 'dislike':
						ele = jQuery('<button/>', {
							"id": "like_post",
							"data-post": post,
							"data-user": user
						}).appendTo(div1);
						ele.html("Like Post");
						ele = jQuery('<button/>', {
							"id": "remove_unlike",
							"data-post": post,
							"data-user": user
						}).appendTo(div2);
						ele.html("Remove Unlike");
						break;

					case 'remove':
						ele = jQuery('<button/>', {
							"id": "like_post",
							"data-post": post,
							"data-user": user
						}).appendTo(div1);
						ele.html("Like Post");
						ele = jQuery('<button/>', {
							"id": "unlike_post",
							"data-post": post,
							"data-user": user
						}).appendTo(div2);
						ele.html("Unlike Post");
						break;

					default:
						alert(response);
				}
				bindLikes();
				$("#like_count").html(response.likes);
				$("#dislike_count").html(response.dislikes);
			} else {
				alert(response);
			}
		} else {
			alert(response);
		}
	}
}
function likeDislikePost(e) {
	var	ele = $(this),
		postid = ele.attr('data-post'),
		userid = ele.attr('data-user'),
		type = ele.attr('id');
/*
	console.log(ele);
	console.log(postid);
	console.log(userid);
	console.log(type);*/
	console.log('creating request object.');
	if(window.XMLHttpRequest) {
		like_dislike_request = new XMLHttpRequest();
	} else {
		like_dislike_request = new ActiveXObject("Microsoft.XMLHTTP");
	}
	like_dislike_request.onreadystatechange = likeDislikeComplete;
	like_dislike_request.open("GET", "relations/like?user="+userid+"&post="+postid+"&task="+type);
	console.log("sending request.");
	like_dislike_request.send();
}