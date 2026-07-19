<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$apiKey = config('services.rest_countries.api_key');
$response = Illuminate\Support\Facades\Http::withOptions(['verify' => false])->withHeaders(['Authorization' => 'Bearer ' . $apiKey])->get('https://api.restcountries.com/countries/v5?limit=1');
$data = $response->json();
unset($data['data']['objects']);
print_r($data);
