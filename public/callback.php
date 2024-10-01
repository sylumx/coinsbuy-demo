<?php
require_once __DIR__ . '/../src/CoinsbuyAPI.php';

$api = new CoinsbuyAPI(false); // Use 'false' for production
$rawData = file_get_contents('php://input');
$callbackResult = $api->handleCallback($rawData);

if (isset($callbackResult['error'])) {
    error_log('Coinsbuy callback error: ' . $callbackResult['error']);
    http_response_code(403);
} else {
    processCallbackData($callbackResult);
    http_response_code(200);
}

echo json_encode(['status' => 'received']);

function processCallbackData($data)
{
    // Implement your logic here
    error_log('Received callback data: ' . print_r($data, true));

    $transactionId = $data['data']['id'] ?? null;
    $newStatus = $data['included'][1]['attributes']['status'] ?? null;

    if ($transactionId && $newStatus) {
        // updateTransactionStatus($transactionId, $newStatus);
        // sendNotificationToUser($transactionId, $newStatus);
        error_log("Updated transaction $transactionId to status: $newStatus");
    }
}