<?php
include 'db_connect.php';

$api_keys = [
    "1GDUu2YezC0EK7qoI5nRGLCeNsoVTm28",
    "tbwB_RszI_PN6CbjgZkNx4mPTL9gjbSF",
    "xKUsip2SKm4yT4a3AOMNVjulX_VGZ1VN",
    "h43VrHWchmYxje_qvHehnjr1OAc_gLqQ",
    "x_NG9NB8qzyI9XWKKc8UmKMxzbb_XSpm"
];

function get_api_key($api_keys) {
    static $index = -1;
    $index = ($index + 1) % count($api_keys);
    return $api_keys[$index];
}

function fetch_api_data($url) {
    $response = file_get_contents($url);
    if ($response === FALSE) {
        return null;
    }
    return json_decode($response, true);
}

$result = $conn->query("SELECT * FROM portfolio");

if ($result->num_rows > 0) {
    $portfolio = [];
    while ($row = $result->fetch_assoc()) {
        $symbol = $row['symbol'];
        $amount = $row['amount'];

        $api_key = get_api_key($api_keys);
        $date = date('Y-m-d', strtotime('-1 day')); // Previous business day
        $price_url = "https://api.polygon.io/v1/open-close/$symbol/$date?adjusted=true&apiKey=$api_key";
        $price_data = fetch_api_data($price_url);

        if ($price_data && isset($price_data['open']) && isset($price_data['close'])) {
            $current_price = $price_data['open']; // Assuming open price as the current price for simplicity
            $buy_price = $row['previous_close_price'];
            $profit_loss = ($current_price - $buy_price) * $amount;
            $profit_loss_percent = (($current_price - $buy_price) / $buy_price) * 100;

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
