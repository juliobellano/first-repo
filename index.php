<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Portfolio Tracker</title>  
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script>
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
        async function showPortfolio() {
            const response = await fetch('get_portfolio.php');
            const portfolio = await response.json();
            const portfolioTable = document.getElementById("portfolioTable");
            portfolioTable.innerHTML = "<tr><th>Symbol</th><th>Name</th><th>Amount</th><th>Current Price</th><th>Buy Price</th><th>Profit/Loss</th><th>Profit/Loss (%)</th></tr>";
            portfolio.forEach(stock => {
                const row = document.createElement("tr");
                row.innerHTML = `<td>${stock.symbol}</td><td>${stock.name}</td><td>${stock.amount}</td><td>${stock.current_price}</td><td>${stock.buy_price}</td><td>${stock.profit_loss}</td><td>${stock.profit_loss_percent}%</td>`;
                portfolioTable.appendChild(row);
            });
        }
    </script>
</head>

<body>
    <div class="container">
        <h1>Stock Portfolio Tracker</h1>
        <form action="add_stock.php" method="get">
            
            <label for="stockSearch">Search for a stock:</label><br>
            <input type="text" id="stockSearch" name="stockSearch" onkeyup="updateDropdown()"><br>
            
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
        <br><br>
        <button type="button">Show Portfolio</button>

        <h2>Portfolio</h2>
        <table id="portfolioTable">
            <tr>
                <th>Symbol</th>
                <th>Name</th>
                <th>Amount</th>
                <th>Current Price</th>
                <th>Buy Price</th>
                <th>Profit/Loss</th>
                <th>Profit/Loss (%)</th></tr>
        </table>
    </div>
</body>
</html>
