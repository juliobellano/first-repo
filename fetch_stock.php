<?php
if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $api_key = "EoORQU1v7jsIphhGyFHr3opEsSkHGkqX";
    $api_url = "https://api.polygon.io/v3/reference/tickers?search=$query&active=true&apiKey=$api_key";
    $api_response = file_get_contents($api_url);
    echo $api_response;
}
?>
