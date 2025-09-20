<?php

require_once __DIR__ . '/../system/lib/autoload.php';
require_once __DIR__ . '/../system/lib/init.php';
use App\Lib\Currencies;

// Get config
require_once(__DIR__ . '/../app/config/config.php');

// Get constants
require_once(__DIR__ . '/../constants.php');

// Parse command line arguments for action in form scriptname [action] as a string
$action = isset($argv[1]) ? $argv[1] : '';

switch ($action) {
    case 'run':
        updateRates();
        break;
    default:
        echo "Exchange Rate Api";
        exit(1);
        break;
}

function updateRates()
{
    $apiKey = JSON_RATES_KEY;
    $url = "http://apilayer.net/api/live?access_key={$apiKey}&currencies=USD,GBP,EUR,KES&source=ZAR&format=1";

    $json = file_get_contents($url);
    if ($json === false) {
        echo "Failed to fetch exchange rates.\n";
        exit(1);
    }

    $data = json_decode($json, true);

    if ($data['success']) {
       
        $usdToZar = 1 / $data['quotes']['ZARUSD']; // 1 USD in ZAR
        $gbpToZar = 1 / $data['quotes']['ZARGBP']; // 1 GBP in ZAR
        $eurToZar = 1 / $data['quotes']['ZAREUR']; // 1 EUR in ZAR
        $kesToZar = 1 / $data['quotes']['ZARKES']; // 1 KES in ZAR

        echo "1 USD = {$usdToZar} ZAR\n";
        echo "1 GBP = {$gbpToZar} ZAR\n";
        echo "1 EUR = {$eurToZar} ZAR\n";
        echo "1 KES = {$kesToZar} ZAR\n";

        $rates = [
            'USD' => $usdToZar,
            'GBP' => $gbpToZar,
            'EUR' => $eurToZar,
            'KES' => $kesToZar,
        ];

        foreach ($rates as $code => $rate) {
            Currencies::updateCurrencies($code, $rate);
        }
    } else {
        echo "API error: " . json_encode($data);
    }
}