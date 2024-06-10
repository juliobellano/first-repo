CREATE DATABASE IF NOT EXISTS stock_portfolio;

USE stock_portfolio;

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


