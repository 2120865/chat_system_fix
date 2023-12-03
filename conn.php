<?php
$host = 'db';
$dbusername = 'root';
$dbpassword = 'root';
$database = 'chat_system';
$port = 3306;

$conn = new mysqli($host, $dbusername, $dbpassword, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>