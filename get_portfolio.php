<?php
include 'db_connect.php';

// List of API keys
$api_keys = [
    "1GDUu2YezC0EK7qoI5nRGLCeNsoVTm28",
    "tbwB_RszI_PN6CbjgZkNx4mPTL9gjbSF",
    "xKUsip2SKm4yT4a3AOMNVjulX_VGZ1VN",
    "h43VrHWchmYxje_qvHehnjr1OAc_gLqQ",
    "x_NG9NB8qzyI9XWKKc8UmKMxzbb_XSpm"
];

// Function to get the next API key in a round-robin fashion
function get_api_key($api_keys) {
    static $index = -1;
    $index = ($index + 1) % count($api_keys);
    return $api_keys[$index];
}

// Function to fetch API data
function fetch_api_data($url) {
    $response = file_get_contents($url);
    if ($response === FALSE) {
        return null;
    }
    return json_decode($response, true);
}

// Fetch all portfolio records from the database
$result = $conn->query("SELECT * FROM portfolio");

$portfolio = [];
if ($result === FALSE) {
    die("Error executing query: " . $conn->error);
} else if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $symbol = $row['symbol'];
        $amount = $row['amount'];
        $buy_price = $row['buy_price'];

        // Fetch the previous close price for each stock
        $api_key = get_api_key($api_keys);
        $price_url = "https://api.polygon.io/v2/aggs/ticker/$symbol/prev?adjusted=true&apiKey=$api_key";
        $price_data = fetch_api_data($price_url);

        if ($price_data && isset($price_data['results'][0]['c'])) {
            $current_price = $price_data['results'][0]['c']; // Close price
            $profit_loss = ($current_price - $buy_price) * $amount;
            $profit_loss_percent = (($current_price - $buy_price) / $buy_price) * 100;

            // Update the portfolio with the latest price data
            $update_stmt = $conn->prepare("UPDATE portfolio SET current_price = ?, profit_loss = ?, profit_loss_percent = ? WHERE id = ?");
            $update_stmt->bind_param("dddi", $current_price, $profit_loss, $profit_loss_percent, $row['id']);
            $update_stmt->execute();
            $update_stmt->close();

            // Append the updated data to the portfolio array
            $portfolio[] = [
                'symbol' => $symbol,
                'name' => $row['name'],
                'amount' => $amount,
                'current_price' => $current_price,
                'buy_price' => $buy_price,
                'profit_loss' => $profit_loss,
                'profit_loss_percent' => $profit_loss_percent
            ];
        } else {
            // In case the API call fails, return the data as unavailable
            $portfolio[] = [
                'symbol' => $symbol,
                'name' => $row['name'],
                'amount' => $amount,
                'current_price' => 'N/A',
                'buy_price' => $buy_price,
                'profit_loss' => 'N/A',
                'profit_loss_percent' => 'N/A'
            ];
        }
    }
    echo json_encode($portfolio);
} else {
    echo json_encode([]);
}

$conn->close();
?>
