* Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS stock_portfolio;

* Use the stock_portfolio database
USE stock_portfolio;

* Create the portfolio table
CREATE TABLE portfolio (
 id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
     symbol VARCHAR(10) NOT NULL,
     amount INT NOT NULL,
     buy_price DECIMAL(10, 2) NOT NULL,
 current_price DECIMAL(10, 2) NOT NULL,
 profit_loss DOUBLE NOT NULL,
 profit_loss_percent DOUBLE NOT NULL,
 total_valuation INT NOT NULL
);


