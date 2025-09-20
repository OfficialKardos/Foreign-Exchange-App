<?php
require_once __DIR__ . '/../../../../system/lib/autoload.php';
require_once __DIR__ . '/../../../../system/lib/init.php';
use App\Lib\Currencies;

$currencies = Currencies::getAllCurrencies();

header('Content-Type: application/json; charset=utf-8;');

if (!empty($currencies)) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'msg' => 'Currencies',
        'data' => $currencies
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error_msg' => 'Failed to get currencies.',
        'error' => 'Failed to get currencies.'
    ]);
}
