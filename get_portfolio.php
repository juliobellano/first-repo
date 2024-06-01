<?php
include 'db_connect.php';

$result = $conn->query("SELECT * FROM portfolio");

if ($result->num_rows > 0) {
    $portfolio = [];
    while ($row = $result->fetch_assoc()) {
        $portfolio[] = $row;
    }
    echo json_encode($portfolio);
} else {
    echo json_encode([]);
}

$conn->close();
?>
