<?php
if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $api_key = "YZM8LKW7RDEBELFD6";
    $api_url = "https://www.alphavantage.co/query?function=SYMBOL_SEARCH&keywords=$query&apikey=$api_key";
    $api_response = file_get_contents($api_url);
    echo $api_response;
}
?>
