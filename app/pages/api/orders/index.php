<?php

require_once __DIR__ . '/../../../../system/lib/autoload.php';
require_once __DIR__ . '/../../../../system/lib/init.php';
require_once __DIR__ . '/../../../../vendor/autoload.php';
use App\Lib\Orders;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
header('Content-Type: application/json');

$foreign_amount = trim(filter_input(INPUT_POST, 'foreign_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
$currency_code = trim(filter_input(INPUT_POST, 'currency_code', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$zar_amount = trim(filter_input(INPUT_POST, 'zar_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
$currency_id = trim(filter_input(INPUT_POST, 'currency_id', FILTER_SANITIZE_NUMBER_INT));
$exchange_rate = trim(filter_input(INPUT_POST, 'exchange_rate', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
$surcharge = trim(filter_input(INPUT_POST, 'surcharge', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
$surcharge_amount = trim(filter_input(INPUT_POST, 'surcharge_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));

if ($currency_code == 'EUR') {
    $discount_percentage = EUR_DISCOUNT;
} else {
    $discount_percentage = '0.00';
}

$surcharge = $surcharge === '' ? 0 : floatval($surcharge);
$surcharge_decimal = $surcharge / 100;

$order = new Orders($params);
$order->currency_id = $currency_id;
$order->exchange_rate = $exchange_rate;
$order->surcharge_percentage = $surcharge_decimal;
$order->discount_percentage = $discount_percentage;
$order->foreign_amount = $foreign_amount;
$order->zar_amount = $zar_amount;
$order->surcharge_amount = $surcharge_amount;

if ($order_id = $order->create()) {
    if ($currency_code == 'GBP') {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USER;
            $mail->Password   = MAIL_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = MAIL_PORT;

            $mail->setFrom('no-reply@example.com', 'Order System');
            $mail->addAddress('admin@example.com'); // change to your recipient

            $mail->isHTML(true);
            $mail->Subject = 'New GBP Order';
            $mail->Body    = "
                <h2>New GBP Order</h2>
                <p><strong>OrderNumber:</strong> {$order_id}</p>
                <p><strong>Foreign Amount:</strong> {$foreign_amount}</p>
                <p><strong>Exchange Rate:</strong> {$exchange_rate}</p>
                <p><strong>Surcharge %:</strong> {$surcharge}%</p>
                <p><strong>Surcharge Amount:</strong> {$surcharge_amount}</p>
                <p><strong>ZAR Amount:</strong> {$zar_amount}</p>
            ";

            $mail->send();

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error_msg' => 'Failed to place order, mail sending error.',
                'error' => 'Failed to place order.'
            ]);
        }
    }
    http_response_code(200);
    echo json_encode(value: [
        'success' => true,
        'msg' => 'Order Successfully placed.',
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error_msg' => 'Failed to place order.',
        'error' => 'Failed to place order.'
    ]);
}
