document.getElementById('stock-search-input').addEventListener('input', function() {
    let query = this.value;
    if (query.length >= 2) {
        fetch(`fetch_stock.php?query=${query}`)
            .then(response => response.json())
            .then(data => {
                let results = data.results;
                let searchResultsDiv = document.getElementById('search-results');
                searchResultsDiv.innerHTML = '';
                results.forEach(stock => {
                    let div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.textContent = `${stock.symbol} - ${stock.name}`;
                    div.addEventListener('click', function() {
                        addToPortfolio(stock.symbol, stock.name);
                        document.getElementById('stock-search-input').value = stock.symbol;
                        searchResultsDiv.innerHTML = '';
                        searchResultsDiv.style.display = 'none';
                    });
                    searchResultsDiv.appendChild(div);
                });
                searchResultsDiv.style.display = 'block';
            });
    } else {
        document.getElementById('search-results').style.display = 'none';
    }
});

function addToPortfolio(symbol, name) {
    fetch('add_stock.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ symbol, name })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Stock added to portfolio!');
        } else {
            alert('Failed to add stock.');
        }
    });
}

document.getElementById('show-portfolio-button').addEventListener('click', function() {
    fetch('get_portfolio.php')
        .then(response => response.json())
        .then(data => {
            let portfolioListDiv = document.getElementById('portfolio-list');
            portfolioListDiv.innerHTML = '';
            data.stocks.forEach(stock => {
                let div = document.createElement('div');
                div.textContent = `${stock.symbol} - ${stock.name}`;
                portfolioListDiv.appendChild(div);
            });
        });
});
