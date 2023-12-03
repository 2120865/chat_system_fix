<?php
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["password"])) {
        
		include('conn.php');

        try {
            $stmt = $conn->prepare("SELECT * FROM `user` WHERE username=? AND password=?");
            $stmt->bind_param("ss", $_POST["username"], md5($_POST["password"]));
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                if ($row['access'] == 1) {
                    $_SESSION['id'] = $row['userid'];
                    ?>
                    <script>
                        window.alert('Login Success, Welcome Admin!');
                        window.location.href='admin/';
                    </script>
                    <?php
                } else {
                    $_SESSION['id'] = $row['userid'];
                    ?>
                    <script>
                        window.alert('Login Success, Welcome User!');
                        window.location.href='user/';
                    </script>
                    <?php
                }
            } else {
                $_SESSION['msg'] = "Login Failed, Invalid Input!";
                header('location: index.php');
            }

            $stmt->close();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        } finally {
            $conn->close();
        }
    }
?>
