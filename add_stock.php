<?php
include 'db_connect.php';

$api_keys = [
    "1GDUu2YezC0EK7qoI5nRGLCeNsoVTm28",
    "tbwB_RszI_PN6CbjgZkNx4mPTL9gjbSF",
    "xKUsip2SKm4yT4a3AOMNVjulX_VGZ1VN" /*,
    "helga api",
    "timo api",
    "shiena api",
    "bam api"*/
];

function get_api_key($api_keys) {
    static $index = -1;
    $index = ($index + 1) % count($api_keys);
    return $api_keys[$index];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $symbol = $_POST['stockSymbol'];
    $amount = $_POST['stockAmount'];

    // Fetch stock name using Polygon.io API
    $api_key = get_api_key($api_keys);
    $api_url = "https://api.polygon.io/v3/reference/tickers?ticker=$symbol&apiKey=$api_key";
    $api_response = file_get_contents($api_url);

    if ($api_response === FALSE) {
        die("Error: Unable to fetch stock information.");
    }

    $api_data = json_decode($api_response, true);

    if (isset($api_data['results']) && !empty($api_data['results'])) {
        $name = $api_data['results'][0]['name'];

        // Fetch current price
        $api_key = get_api_key($api_keys);
        $price_url = "https://api.polygon.io/v1/last_quote/stocks/$symbol?apiKey=$api_key";
        $price_response = file_get_contents($price_url);

        if ($price_response === FALSE) {
            die("Error: Unable to fetch stock price.");
        }

        $price_data = json_decode($price_response, true);

        if (isset($price_data['last']['price'])) {
            $current_price = $price_data['last']['price'];
            $buy_price = $current_price;  // For simplicity, using the current price as the buy price

            // Calculate profit/loss
            $profit_loss = 0;
            $profit_loss_percent = 0;

            $stmt = $conn->prepare("INSERT INTO portfolio (symbol, name, amount, current_price, buy_price, profit_loss, profit_loss_percent) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiiddd", $symbol, $name, $amount, $current_price, $buy_price, $profit_loss, $profit_loss_percent);

            if ($stmt->execute()) {
                echo "Stock added successfully.";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            die("Error: Unable to fetch the current price from Polygon.io.");
        }
    } else {
        die("Error: Stock not found.");
    }
}

$conn->close();
?>

<br>
<a href="index.php">Back to Portfolio</a>
