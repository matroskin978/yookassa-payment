<?php

use YooKassa\Model\Notification\NotificationEventType;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationWaitingForCapture;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

$source = file_get_contents('php://input');
$requestBody = json_decode($source, true);

//file_put_contents(__DIR__ . '/success.txt', "<pre>" . print_r($requestBody, 1) . "</pre>" . PHP_EOL, FILE_APPEND);

try {
    /*$notification = ($requestBody['event'] === NotificationEventType::PAYMENT_SUCCEEDED)
        ? new NotificationSucceeded($requestBody)
        : new NotificationWaitingForCapture($requestBody);*/
    $payment_status = $requestBody['object']['status'] ?? '';
    $payment_id = $requestBody['object']['id'] ?? '';
    $payment_paid = $requestBody['object']['paid'] ?? '';
    $payment_amount = $requestBody['object']['amount']['value'] ?? '';
    $payment_order_id = $requestBody['object']['metadata']['orderNumber'] ?? '';
    if ($payment_status == 'succeeded' && $payment_paid) {
        $stmt = $pdo->prepare("SELECT price FROM orders WHERE id = ?");
        $stmt->execute([$payment_order_id]);
        $correct_amount = $stmt->fetchColumn();
        if ($correct_amount == $payment_amount) {
            $stmt = $pdo->prepare("UPDATE orders SET status = 1 WHERE id = ?");
            $stmt->execute([$payment_order_id]);
        } else {
            file_put_contents(__DIR__ . '/errors.txt', "Incorrect amount: {$payment_amount}" . PHP_EOL, FILE_APPEND);
        }
    }
} catch (Exception $e) {
    file_put_contents(__DIR__ . '/errors.txt', $e->getMessage() . PHP_EOL, FILE_APPEND);
}
