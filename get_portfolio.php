<?php
include 'db_connect.php';

$api_key = "EoORQU1v7jsIphhGyFHr3opEsSkHGkqX";
$result = $conn->query("SELECT * FROM portfolio");

if ($result->num_rows > 0) {
    $portfolio = [];
    while ($row = $result->fetch_assoc()) {
        $symbol = $row['symbol'];
        $amount = $row['amount'];
        // Fetch current price using Polygon.io API
        $price_url = "https://api.polygon.io/v1/last_quote/stocks/$symbol?apiKey=$api_key";
        $price_response = file_get_contents($price_url);
        $price_data = json_decode($price_response, true);

        if (isset($price_data['last']['price'])) {
            $current_price = $price_data['last']['price'];

            $portfolio[] = [
                'symbol' => $symbol,
                'name' => $row['name'],
                'amount' => $amount,
                'current_price' => $current_price,
                // For simplicity, assuming a fixed buy price. In a real scenario, you would store the buy price.
                'buy_price' => 100,
                'profit_loss' => ($current_price - 100) * $amount,
                'profit_loss_percent' => (($current_price - 100) / 100) * 100
            ];
        } else {
            $portfolio[] = [
                'symbol' => $symbol,
                'name' => $row['name'],
                'amount' => $amount,
                'current_price' => 'N/A',
                'buy_price' => 'N/A',
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
