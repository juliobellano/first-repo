<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $symbol = $_POST['stockSymbol'];

    // Fetch stock name using Alpha Vantage API
    $api_key = "YZM8LKW7RDEBELFD6";
    $api_url = "https://www.alphavantage.co/query?function=SYMBOL_SEARCH&keywords=$symbol&apikey=$api_key";
    $api_response = file_get_contents($api_url);
    $api_data = json_decode($api_response, true);

    if (!empty($api_data['bestMatches'])) {
        $name = $api_data['bestMatches'][0]['2. name'];

        $stmt = $conn->prepare("INSERT INTO portfolio (symbol, name) VALUES (?, ?)");
        $stmt->bind_param("ss", $symbol, $name);

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
