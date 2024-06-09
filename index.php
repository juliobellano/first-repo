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
<html>
<style>
body {
    background-color:rgb(249,225,196);
    font-family:Verdana, Geneva, Tahoma, sans-serif;
    margin: 10px;
}

.container {
    max-width: 600px;
    margin: 30px auto;
}

input[type=text], select {
    width: 100%;
    padding: 12px;
    margin: 10px auto;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

input[type=number] {
    width: 100%;
    padding: 12px;
    margin: 10px auto;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

.col-1-2 {
    width: 49%;
    display: inline-block;
    vertical-align: top;
}
input[type=submit], button {
    width: 100%;
    background-color: #45a049;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

table {
    font-size: 11px;
    width: 100%;
    border-collapse: collapse;
}

table, th, td {
    border: 3px solid black;
}

th, td {
    padding: 12px;
}

th {
    background-color: #f2f2f2;
}

.group:before, 
.group:after {
    content:"";
    display: table;
}
.group:after {
    clear: both;
}
.group {
    clear: both;
    zoom: 1;
}
    
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Portfolio Tracker</title>   
    <link rel="stylesheet" href="styles.css">
    <script>
        $api_keys = [
            "1U3BHsFpewhF_TQLOMop5WHAmrtCEubs"
        ];
        function updateDropdown(){
            const query = document.getElementById("stockSearch").value;
            if(query.length > 2){ searchStocks(query); }
        }
        
        async function searchStocks(name){
            const response = await fetch(`fetch_stock.php?query=${name}`);
            const data = await response.json();
            const dropdown = document.getElementById("stockDropdown");
            dropdown.innerHTML = "";
            data.results.forEach(stock => {
                const option = document.createElement("option");
                option.value = stock["ticker"];
                option.textContent = `${stock["ticker"]} - ${stock["name"]}`;
                dropdown.appendChild(option);
            });
        }

        function togglePortfolioTable() {
            var table = document.querySelector('.portfolioTable');
            table.style.display = table.style.display === 'none' ? 'table' : 'none';
        }
    </script>
</head>

<body>
    <div class="container">
        <h1>Stock Portfolio Tracker</h1>
        <form action="add_stock.php" method="get">
            
            <label for="stockSearch">Search for a stock:</label><br>
            <input type="text" id="stockSearch" name="stockSymbol" onkeyup="updateDropdown()" required><br>
            
            <label for="stockDropdown">Select stock:</label>
            <select id="stockDropdown" name="stockSymbol"></select><br>

            <section class="col-1-2">
                <label for="stockAmount">Amount:</label><br>
                <input type="number" id="stockAmount" name="stockAmount" required><br>
            </section>
            <section class="col-1-2">
                <label for="buyPrice">Buy Price :</label><br>
                <input type="number" id="buyPrice" name="buyPrice" required><br>
            </section>

            <input type="submit" value="Add to Portfolio">
        </form>
        <br>
        <button onclick="togglePortfolioTable()">Sell Stock</button>

        <h2>Portfolio</h2>
        <table id="portfolioTable">
            <thead>
                <tr>
                    <th>Symbol</th>
                    <th>Amount</th>
                    <th>Current Price</th>
                    <th>Buy Price</th>
                    <th>Profit/Loss</th>
                    <th>Profit/Loss (%)</th>
                    <th>Valuation</th>
                </tr>
            </thead>

            <tbody>
                <?php if(count($portfolio) > 0) :?>
                    <?php foreach($portfolio as $stock): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stock['symbol']); ?></td>
                            <td><?php echo htmlspecialchars($stock['amount']); ?></td>
                            <td><?php echo '$'. htmlspecialchars($stock['current_price']); ?></td>
                            <td><?php echo '$'. htmlspecialchars($stock['buy_price']); ?></td>
                            <td><?php echo '$'. htmlspecialchars($stock['profit_loss']); ?></td>
                            <td><?php echo htmlspecialchars($stock['profit_loss_percent']).'%'; ?></td>
                            <td><?php echo htmlspecialchars($stock['total_valuation']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7">No stocks in portfolio.</td>
                </tr>
                <?php endif; ?>
                
            </tbody>
        </table>
    </div>
</body>
</html>
