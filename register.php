<?php
	session_start();

	include('conn.php');

	function check_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {

		$username=check_input($_POST['username']);
		
		if (!preg_match("/^[a-zA-Z0-9_]*$/",$username)) {
			$_SESSION['sign_msg'] = "Username should not contain space and special characters!"; 
			header('location: signup.php');
		}
		else{
			
			$fusername=$username;
			
			$password = check_input($_POST["password"]);
			$fpassword=md5($password);
			$fname = check_input($_POST["name"]);
			try{
				mysqli_query($conn,"insert into `user` (uname, username, password, photo, access) values ('$fname', '$fusername', '$fpassword', '', '2')");
				$_SESSION['msg'] = "Sign up successful. You may login now!"; 
				header('location: index.php');
			} catch (Exception $e) {
				echo "Error: " . $e->getMessage();
			} finally {
				$conn->close();
			}
		}
	}
?>