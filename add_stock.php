
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// List of API keys
$api_keys = [
    "EoORQU1v7jsIphhGyFHr3opEsSkHGkqX",
    "tbwB_RszI_PN6CbjgZkNx4mPTL9gjbSF",
    "xKUsip2SKm4yT4a3AOMNVjulX_VGZ1VN",
    "h43VrHWchmYxje_qvHehnjr1OAc_gLqQ",
    "x_NG9NB8qzyI9XWKKc8UmKMxzbb_XSpm"
];

// Function to get the next API key in a round-robin fashion
function get_api_key($api_keys) {
    static $index = -1;
    $index = ($index + 1) % count($api_keys);
    return $api_keys[$index];
}

// Function to fetch API data
function fetch_api_data($url) {
    $response = file_get_contents($url);
    if ($response === FALSE) {
        return null;
    }
    return json_decode($response, true);
}

// Fetch stock data based on the search query
if (isset($_GET['query'])) {
    $query = urlencode($_GET['query']);
    $api_key = get_api_key($api_keys);
    $api_url = "https://api.polygon.io/v3/reference/tickers?search=$query&market=stocks&active=true&apiKey=$api_key";
    $api_data = fetch_api_data($api_url);

    if ($api_data) {
        echo json_encode($api_data);
    } else {
        echo json_encode(['error' => 'Error fetching stock data from the API.']);
    }
}
?>
