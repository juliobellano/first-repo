<?php
include 'db_connect.php';

// Fetch all portfolio records from the database
$result = $conn->query("SELECT * FROM portfolio");

$portfolio = [];
if ($result === FALSE) {
    die("Error executing query: " . $conn->error);
} else if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $portfolio[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Portfolio Tracker</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
        }
        .back-link {
            text-decoration: none;
            color: #007bff;
        }
        .portfolio-table {
            display: none;
        }
    </style>
    <script>
        // List of API keys
        const apiKeys = [
            "EoORQU1v7jsIphhGyFHr3opEsSkHGkqX",
            "tbwB_RszI_PN6CbjgZkNx4mPTL9gjbSF",
            "xKUsip2SKm4yT4a3AOMNVjulX_VGZ1VN",
            "h43VrHWchmYxje_qvHehnjr1OAc_gLqQ",
            "x_NG9NB8qzyI9XWKKc8UmKMxzbb_XSpm"
        ];

        let apiKeyIndex = 0;

        // Function to get the next API key
        function getApiKey() {
            const apiKey = apiKeys[apiKeyIndex];
            apiKeyIndex = (apiKeyIndex + 1) % apiKeys.length;
            return apiKey;
        }

        // Function to fetch stock details
        function fetchStockDetails() {
            var stockSymbol = document.getElementById("stockSymbol").value;
            if (stockSymbol.length >= 2) {
                const apiKey = getApiKey();
                fetch(`https://api.polygon.io/v3/reference/tickers?search=${stockSymbol}&market=stocks&active=true&apiKey=${apiKey}`)
                    .then(response => response.json())
                    .then(data => {
                        var select = document.getElementById("stockSelect");
                        select.innerHTML = ""; // Clear previous options
                        if (data.results && data.results.length > 0) {
                            data.results.forEach(stock => {
                                var option = document.createElement("option");
                                option.value = stock.ticker;
                                option.text = `${stock.name} (${stock.ticker})`;
                                select.appendChild(option);
                            });
                        } else {
                            var option = document.createElement("option");
                            option.value = "";
                            option.text = "No stocks found";
                            select.appendChild(option);
                        }
                    })
                    .catch(error => console.error("Error fetching stock details:", error));
            } else {
                document.getElementById("stockSelect").innerHTML = "";
            }
        }

        function resetStockSelect() {
            var stockSymbol = document.getElementById("stockSymbol").value;
            if (stockSymbol === "") {
                document.getElementById("stockSelect").innerHTML = "";
            }
        }

        function togglePortfolioTable() {
            var table = document.querySelector('.portfolio-table');
            table.style.display = table.style.display === 'none' ? 'table' : 'none';
        }
    </script>
</head>
<body>
    <h1>Stock Portfolio Tracker</h1>
    <form action="add_stock.php" method="GET">
        <label for="stockSymbol">Search Stock:</label>
        <input type="text" id="stockSymbol" name="stockSymbol" oninput="fetchStockDetails()" oninput="resetStockSelect()" required>
        <br>
        <label for="stockSelect">Select Stock:</label>
        <select id="stockSelect" name="stockSymbol" required></select>
        <br>
        <label for="stockAmount">Amount:</label>
        <input type="number" id="stockAmount" name="stockAmount" required>
        <br>
        <button type="submit">Add Stock</button>
    </form>
    <br>
    <button onclick="togglePortfolioTable()">View Portfolio</button>
    
    <table class="portfolio-table">
        <thead>
            <tr>
                <th>Symbol</th>
                <th>Name</th>
                <th>Amount</th>
                <th>Buy Price</th>
                <th>Current Price</th>
                <th>Profit/Loss</th>
                <th>Profit/Loss (%)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($portfolio) > 0): ?>
                <?php foreach ($portfolio as $stock): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($stock['symbol']); ?></td>
                        <td><?php echo htmlspecialchars($stock['name']); ?></td>
                        <td><?php echo htmlspecialchars($stock['amount']); ?></td>
                        <td><?php echo htmlspecialchars($stock['buy_price']); ?></td>
                        <td><?php echo htmlspecialchars($stock['current_price']); ?></td>
                        <td><?php echo htmlspecialchars($stock['profit_loss']); ?></td>
                        <td><?php echo htmlspecialchars($stock['profit_loss_percent']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No stocks in portfolio.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
