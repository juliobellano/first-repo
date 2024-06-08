<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

echo "<pre>";
print_r($_GET);
print_r($_POST);
echo "</pre>";

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

$symbol = '';
$amount = 0;
$name = '';
$previous_close_price = 0;
$open_price = 0;
$prices_fetched = false;

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['symbol']) && isset($_GET['name'])) {
    $symbol = $_GET['symbol'];
    $name = $_GET['name'];
} 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fetch_prices'])) {
    $symbol = $_POST['symbol'];
    $name = $_POST['name'];
    $amount = $_POST['stockAmount'];

    // Fetch previous close prices using Polygon.io API
    $api_key = get_api_key($api_keys);
    $price_url = "https://api.polygon.io/v2/aggs/ticker/$symbol/prev?adjusted=true&apiKey=$api_key";
    $price_response = file_get_contents($price_url);

    if ($price_response === FALSE) {
        die("Error: Unable to fetch previous close prices.");
    }

    $price_data = json_decode($price_response, true);

    if (isset($price_data['results'][0]['c']) && isset($price_data['results'][0]['o'])) {
        $previous_close_price = $price_data['results'][0]['c'];
        $open_price = $price_data['results'][0]['o'];
        $prices_fetched = true;
    } else {
        echo "Error: Unable to fetch the previous close price from Polygon.io.";
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_add'])) {
    // Insert stock details into the database
    $symbol = $_POST['symbol'];
    $name = $_POST['name'];
    $amount = $_POST['amount'];
    $previous_close_price = $_POST['previous_close_price'];
    $open_price = $_POST['open_price'];

    $stmt = $conn->prepare("INSERT INTO portfolio (symbol, name, amount, buy_price, current_price, open_price, previous_close_price, profit_loss, profit_loss_percent) VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0)");
    $stmt->bind_param("ssiddid", $symbol, $name, $amount, $previous_close_price, $previous_close_price, $open_price, $previous_close_price);
    $stmt->execute();
    $stmt->close();

    echo "Stock added successfully.";
    $prices_fetched = false; // Reset flag to show the form again
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Stock</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Add Stock</h1>

    <?php if (!$prices_fetched && !empty($symbol) && !empty($name)): ?>
        <form action="add_stock.php" method="post">
            <input type="hidden" name="symbol" value="<?php echo htmlspecialchars($symbol); ?>">
            <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
            <label for="stockAmount">Amount:</label>
            <input type="number" id="stockAmount" name="stockAmount" required>
            <button type="submit" name="fetch_prices">Fetch Prices</button>
        </form>
    <?php elseif ($prices_fetched): ?>
        <!-- Display fetched data for review -->
        <p><strong>Stock Symbol:</strong> <?php echo htmlspecialchars($symbol); ?></p>
        <p><strong>Stock Name:</strong> <?php echo htmlspecialchars($name); ?></p>
        <p><strong>Previous Close Price:</strong> <?php echo htmlspecialchars($previous_close_price); ?></p>
        <p><strong>Open Price:</strong> <?php echo htmlspecialchars($open_price); ?></p>

        <form action="add_stock.php" method="post">
            <input type="hidden" name="symbol" value="<?php echo htmlspecialchars($symbol); ?>">
            <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
            <input type="hidden" name="amount" value="<?php echo htmlspecialchars($amount); ?>">
            <input type="hidden" name="previous_close_price" value="<?php echo htmlspecialchars($previous_close_price); ?>">
            <input type="hidden" name="open_price" value="<?php echo htmlspecialchars($open_price); ?>">
            <button type="submit" name="confirm_add">Confirm and Add Stock</button>
        </form>
    <?php else: ?>
        <p>Error: No stock information provided. Please go back and try again.</p>
    <?php endif; ?>

    <form action="index.php">
        <button type="submit">Back</button>
    </form>
</body>
</html>
