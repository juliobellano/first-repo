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

$result = $conn->query("SELECT * FROM portfolio");

if ($result->num_rows > 0) {
    $portfolio = [];
    while ($row = $result->fetch_assoc()) {
        $symbol = $row['symbol'];
        $amount = $row['amount'];

        // Fetch current price using Polygon.io API
        $api_key = get_api_key($api_keys);
        $price_url = "https://api.polygon.io/v1/last_quote/stocks/$symbol?apiKey=$api_key";
        $price_response = file_get_contents($price_url);
        $price_data = json_decode($price_response, true);

        if (isset($price_data['last']['price'])) {
            $current_price = $price_data['last']['price'];
            $buy_price = $row['buy_price'];
            $profit_loss = ($current_price - $buy_price) * $amount;
            $profit_loss_percent = (($current_price - $buy_price) / $buy_price) * 100;

            // Update the database
            $update_stmt = $conn->prepare("UPDATE portfolio SET current_price = ?, profit_loss = ?, profit_loss_percent = ? WHERE id = ?");
            $update_stmt->bind_param("dddi", $current_price, $profit_loss, $profit_loss_percent, $row['id']);
            $update_stmt->execute();
            $update_stmt->close();

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
