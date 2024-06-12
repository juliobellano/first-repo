# Step-by-Step Instructions

Follow these steps to set up your project:

1. **Navigate to the web root directory**:
    ```bash
    cd /var/www/html
    ```

2. **Clone the repository from GitHub**:
    ```bash
    sudo git clone https://github.com/juliobellano/first-repo.git
    ```

3. **Access the MySQL database as the root user**:
    ```bash
    sudo mysql -u root -p
    ```

4. **Create a new database named `stock_portfolio`**:
    ```sql
    CREATE DATABASE stock_portfolio;
    ```

5. **Exit the MySQL prompt**:
    ```sql
    exit;
    ```

6. **Import the database schema into the `stock_portfolio` database**:
    ```bash
    sudo mysql -u portfolio_user -p stock_portfolio < /var/www/html/stock-portfolio-tracker/schema.sql
    ```

7. **Access the MySQL database as the root user again**:
    ```bash
    sudo mysql -u root -p
    ```

8. **Select the `stock_portfolio` database**:
    ```sql
    use stock_portfolio;
    ```

9. **Describe the `portfolio` table**:
    ```sql
    describe portfolio;
    ```

10. **Exit the MySQL prompt**:
    ```sql
    exit;
    ```

11. **Start a local PHP server**:
    ```bash
    php -S 0.0.0.0:8000
    ```

Follow these instructions carefully to set up your project and ensure it is running correctly.
