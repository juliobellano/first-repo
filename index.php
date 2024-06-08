<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Portfolio Tracker</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        // List of API keys
        const apiKeys = [
            "1GDUu2YezC0EK7qoI5nRGLCeNsoVTm28",
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
    </script>
</head>
<body>
    <h1>Stock Portfolio Tracker</h1>
    <form action="add_stock.php" method="POST">
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
    <a href="get_portfolio.php">View Portfolio</a>
</body>
</html>
