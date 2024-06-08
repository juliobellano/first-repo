<?php
// Set your Polygon.io API key here
$apiKey = 'EoORQU1v7jsIphhGyFHr3opEsSkHGkqX';

// Set the symbol for Apple Inc.
$symbol = 'AAPL';

// URL to fetch the stock price
$url = "https://api.polygon.io/v1/last/stocks/$symbol?apiKey=$apiKey";

// Initialize cURL session
$ch = curl_init();

// Set the URL and other options for cURL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute cURL session and store the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    // Decode the JSON response
    $data = json_decode($response, true);
    
    // Check if the data is valid
    if (isset($data['status']) && $data['status'] == 'success') {
        $price = $data['last']['price'];
        echo "The current stock price of Apple (AAPL) is: $" . $price;
    } else {
        echo "Failed to fetch stock price data.";
    }
}

// Close cURL session
curl_close($ch);
?>
