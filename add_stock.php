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

        // Fetch current price
        $price_url = "https://api.polygon.io/v1/last_quote/stocks/$symbol?apiKey=$api_key";
        $price_response = file_get_contents($price_url);
        $price_data = json_decode($price_response, true);
        $current_price = $price_data['last']['price'];
        $buy_price = $current_price;  // For simplicity, using the current price as the buy price

        // Calculate profit/loss
        $profit_loss = 0;
        $profit_loss_percent = 0;

        $stmt = $conn->prepare("INSERT INTO portfolio (symbol, name, amount, current_price, buy_price, profit_loss, profit_loss_percent) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiidd", $symbol, $name, $amount, $current_price, $buy_price, $profit_loss, $profit_loss_percent);

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

<br>
<a href="index.php">Back to Portfolio</a>
