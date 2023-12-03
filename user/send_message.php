<?php
	session_start();
	include('../conn.php');
	date_default_timezone_set('Europe/Berlin');
	if(isset($_POST['msg'])){		
		$msg=$_POST['msg'];
		$id=$_POST['id'];
		$currentDateTime = date('Y-m-d H:i:s');

		$query = "INSERT INTO `chat` (chatroomid, message, userid, chat_date) VALUES ('$id', '$msg', '" . $_SESSION['id'] . "', '$currentDateTime')";

		mysqli_query($conn, $query) or die(mysqli_error());
	}
?>
