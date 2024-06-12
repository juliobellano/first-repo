<?php
include 'db_connect.php';

$api_keys = [
    "1U3BHsFpewhF_TQLOMop5WHAmrtCEubs"
];

function get_api_key($api_keys) {
    return $api_keys[0];
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $symbol = $_GET['stockSymbol'];
    $amount = $_GET['stockAmount'];
    $buy_price = $_GET['buyPrice'];
    $api_key = get_api_key($api_keys);
    
    // Fetch stock price /v2/snapshot/locale/us/markets/stocks/tickers/{stocksTicker}
    $api_url = "https://api.polygon.io/v2/snapshot/locale/us/markets/stocks/tickers/$symbol?apiKey=$api_key";
    $api_response = file_get_contents($api_url);
    if ($api_response === FALSE) { die("Error: Unable to fetch stock information.");}
    $api_data = json_decode($api_response, true);
    
    if(isset($api_data['ticker'])){
        $prices_data = $api_data['ticker']['min'];//min object The most recent minute bar for this ticker.
        $current_price = $prices_data['c'];//The close price for the symbol in the given time period.
    }
    else{ die("Error: Unable to find ticker information.");}
    
    $existence = false;
    $sql = "SELECT * FROM portfolio WHERE symbol=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $symbol);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $existence = true;
        $row = $result->fetch_assoc();
        $buy_price = ((($amount) * ($buy_price)) + ($row['amount'] * $row['buy_price'])) / ($amount + $row['amount']);
        $amount += $row['amount'];
    }
    $profit_loss = ($current_price - $buy_price) * $amount;
    $profit_loss_percent = (($current_price - $buy_price) / $current_price) * 100; 
    $total_valuation = ($current_price) * $amount;
    

    echo "Stock Symbol: " . htmlspecialchars($symbol) . "<br>";
    echo "Stock Amount: " . htmlspecialchars($amount) . "<br>";
    echo "Buy Price: " . htmlspecialchars($buy_price) . "<br>";
    echo "Current price: ". htmlspecialchars($current_price) . "<br>";
    echo "Profit_loss: " . htmlspecialchars($profit_loss) . "<br>";
    echo "profitLoss Percentage: ". htmlspecialchars($profit_loss_percent).'%' . "<br>";
    echo "total_valuation".htmlspecialchars($total_valuation). "<br>";

    if ($existence == true) {
        $sql = "UPDATE portfolio 
                SET amount=?, buy_price=?, current_price=?, profit_loss=?, profit_loss_percent=?, total_valuation=?
                WHERE symbol = ?";
    } else {
        $sql = "INSERT INTO portfolio (symbol, amount, buy_price, current_price, profit_loss, profit_loss_percent, total_valuation) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
    }
    $stmt = $conn->prepare($sql);
    if (!$stmt) { die("Error: " . $conn->error); }
    if ($existence == true) {
        $stmt->bind_param("dddddds", $amount, $buy_price, $current_price, $profit_loss, $profit_loss_percent, $total_valuation, $symbol);
    } else {
        $stmt->bind_param("sdddddd", $symbol, $amount, $buy_price, $current_price, $profit_loss, $profit_loss_percent, $total_valuation);
    }
    if ($stmt->execute()) { echo "Stock added successfully.";}
    else {echo "Error: " . $stmt->error;}
    $stmt->close();
} else {
    die("Error: Stock not found.");
}

$conn->close();
?>

<br>
<a href="index.php">Back to Portfolio</a>
