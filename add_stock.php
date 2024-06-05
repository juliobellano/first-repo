<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $symbol = $_POST['stockSymbol'];
    $amount = $_POST['stockAmount'];

    // Fetch stock name using Polygon.io API
    $api_key = "EoORQU1v7jsIphhGyFHr3opEsSkHGkqX";
    $api_url = "https://api.polygon.io/v3/reference/tickers?ticker=$symbol&apiKey=$api_key";
    $api_response = file_get_contents($api_url);
    $api_data = json_decode($api_response, true);

    if (!empty($api_data['results'])) {
        $name = $api_data['results'][0]['name'];

        $stmt = $conn->prepare("INSERT INTO portfolio (symbol, name, amount) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $symbol, $name, $amount);

        if ($stmt->execute()) {
            echo "Stock added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Stock not found.";
    }
}

$conn->close();
?>
