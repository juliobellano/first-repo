<?php
$servername = "localhost";
$username = "root";
$password = ""; // XAMPP default password for MySQL is empty
$dbname = "stock_portfolio";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
