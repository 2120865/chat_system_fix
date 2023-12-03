<?php
	include('session.php');
	
	$cid=$_POST['chatid'];
	$pass=$_POST['chat_pass'];
	
	$query=mysqli_query($conn,"select * from chatroom where chatroomid='$cid'");
	$row=mysqli_fetch_array($query);

	$stmt = $conn->prepare("SELECT * FROM `chatroom` WHERE chatroomid=? AND chat_password=?");
	$stmt->bind_param("is", $cid, $pass);
	$stmt->execute();
	$result = $stmt->get_result();

	if (mysqli_num_rows($result) === 1){
		mysqli_query($conn,"insert into chat_member (chatroomid, userid) values ('$cid', '".$_SESSION['id']."')");
		header('location: chatroom.php?id='.$cid);
	}
	
	else{
		?>
		<script>
			window.alert('Incorrect Password!');
			window.history.back();
		</script>
		<?php
	}
?>
