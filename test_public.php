<?php
$response = file_get_contents('https://restcountries.com/v3.1/all');
$data = json_decode($response, true);
echo "Count: " . count($data) . "\n";
if (count($data) > 0) {
    echo "First country: " . $data[0]['name']['common'] . "\n";
    echo "Code: " . ($data[0]['cca2'] ?? 'N/A') . "\n";
}
