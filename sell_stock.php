<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == 'GET') {
    $symbol = $_GET['stocksOwned'];
    $amount = (int)$_GET['numberToSell'];
    if (!is_numeric($amount) || $amount <= 0) {
        echo "Invalid number to sell.";
        exit;
    }

    $sql = "SELECT * FROM portfolio WHERE symbol = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $symbol);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo "Symbol not found in portfolio.";
        exit;
    }

    $row = $result->fetch_assoc();
    $old_amount = $row['amount'];
    $buy_price = $row['buy_price'];
    $current_price = $row['current_price'];
    $old_profitLoss = $row['profit_loss'];
    $old_profit_loss_percent = $row['profit_loss_percent'];
    $old_total_valuation = $row['total_valuation'];

    if ($amount > $old_amount) {
        echo "Not enough stock available to sell.";
        exit;
    }

    $new_amount = $old_amount - $amount;
    $new_profitLoss = ($current_price - $buy_price) * $new_amount;
    $new_profitlosspercent = (($current_price - $buy_price) / $current_price) * 100; 
    $total_valuation = $current_price * $new_amount;

    $update_sql = "UPDATE portfolio SET 
                    amount = ?, 
                    profit_loss = ?, 
                    profit_loss_percent = ?, 
                    total_valuation = ? 
                    WHERE symbol = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("iddds", $new_amount, $new_profitLoss, $new_profitlosspercent, $total_valuation, $symbol);

    if ($stmt->execute()) {
        echo "Stock amount modified successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
<br>
<a href="index.php">Back to Portfolio</a>
