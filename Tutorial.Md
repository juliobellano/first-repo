
# Stock Portfolio Tracker Setup Guide on DietPi

This guide will walk you through setting up and running the Stock Portfolio Tracker project on a DietPi system using a Raspberry Pi. We will clone the project from your GitHub repository, configure the necessary components, and get the application up and running.

## Prerequisites

Before you begin, ensure you have the following:

- **A Raspberry Pi**: Running DietPi. [DietPi Download](https://dietpi.com/)
- **SSH Access**: To your DietPi system.
- **GitHub Repository**: The project repository is available at [GitHub](https://github.com/juliobellano/first-repo.git).

## Step 1: Update and Upgrade DietPi

1. **Connect to your Raspberry Pi via SSH**.
   ```bash
   ssh user@your-raspberry-pi-ip
   ```
   Replace `user` with your username and `your-raspberry-pi-ip` with your Pi's IP address.

2. **Update and upgrade your DietPi system**.
   ```bash
   sudo apt update && sudo apt upgrade -y
   ```

## Step 2: Install Required Software

1. **Install Apache, PHP, and MariaDB**.
   ```bash
   sudo apt install -y apache2 php libapache2-mod-php mariadb-server php-mysql
   ```

2. **Start and Enable Apache and MariaDB Services**.
   ```bash
   sudo systemctl start apache2
   sudo systemctl enable apache2
   sudo systemctl start mariadb
   sudo systemctl enable mariadb
   ```

3. **Secure MariaDB Installation**.
   ```bash
   sudo mysql_secure_installation
   ```
   Follow the prompts to secure your MariaDB installation. You can set a root password and remove anonymous users and the test database.

## Step 3: Clone the Project

1. **Navigate to the web root directory**.
   ```bash
   cd /var/www/html
   ```

2. **Clone the GitHub repository**.
   ```bash
   sudo git clone https://github.com/juliobellano/first-repo.git stock-portfolio-tracker
   ```

3. **Set the correct permissions**.
   ```bash
   sudo chown -R www-data:www-data stock-portfolio-tracker
   sudo chmod -R 755 stock-portfolio-tracker
   ```

## Step 4: Configure MariaDB

1. **Log in to the MariaDB console**.
   ```bash
   sudo mysql -u root -p
   ```
   Enter the password you set during the secure installation process.

2. **Create the Database**.
   ```sql
   CREATE DATABASE stock_portfolio;
   ```

3. **Create a User and Grant Permissions**.
   ```sql
   CREATE USER 'portfolio_user'@'localhost' IDENTIFIED BY 'yourpassword';
   GRANT ALL PRIVILEGES ON stock_portfolio.* TO 'portfolio_user'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```

4. **Import the Database Schema**.
   - Copy the SQL script (`schema.sql`) from the cloned repository to your local machine.
   - Import the SQL script into the `stock_portfolio` database:
     ```bash
     sudo mysql -u portfolio_user -p stock_portfolio < /var/www/html/stock-portfolio-tracker/schema.sql
     ```

## Step 5: Configure the Project

1. **Navigate to the Project Directory**.
   ```bash
   cd /var/www/html/stock-portfolio-tracker
   ```

2. **Edit the Database Connection File**.
   - Open `db_connect.php` using a text editor like `nano` or `vi`.
     ```bash
     sudo nano db_connect.php
     ```
   - Ensure the database connection details are correct. Replace the contents with:
     ```php
     <?php
     $servername = "localhost";
     $username = "portfolio_user";
     $password = "yourpassword";
     $dbname = "stock_portfolio";

     // Create connection
     $conn = new mysqli($servername, $username, $password, $dbname);

     // Check connection
     if ($conn->connect_error) {
         die("Connection failed: " . $conn->connect_error);
     }
     ?>
     ```
   - Save and exit the editor (Ctrl + X, then Y, then Enter for nano).

## Step 6: Run the Application

1. **Access the Application**:
   - Open your web browser and go to `http://your-raspberry-pi-ip/stock-portfolio-tracker`.
   - Replace `your-raspberry-pi-ip` with your Pi's IP address.

2. **Using the Application**:
   - Use the "Search Stock" input to find and select stocks.
   - Add stocks to your portfolio by entering the amount and clicking "Add Stock".
   - View your portfolio and track the performance of your stocks.

## Additional Notes

- **Debugging**: If you encounter issues, check the Apache error log for messages:
  ```bash
  sudo tail -f /var/log/apache2/error.log
  ```
  
- **API Keys**: If you hit API request limits, consider obtaining multiple API keys and rotating them as demonstrated in the code.

- **Updating the Project**: Pull the latest changes from the repository using:
  ```bash
  cd /var/www/html/stock-portfolio-tracker
  sudo git pull origin main
  ```

## Conclusion

You have successfully set up and run the Stock Portfolio Tracker application on your DietPi system using a Raspberry Pi. Enjoy tracking your stock portfolio!
