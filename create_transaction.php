<?php
require_once __DIR__ . '/src/CoinsbuyAPI.php';

$api = new CoinsbuyAPI(true); // Use true for sandbox testing

// Create a deposit
$depositResult = $api->createDeposit(100, 'BTC', 'callback.php');
echo "Deposit Result:\n";
print_r($depositResult);

// Create a payout
$payoutResult = $api->createPayout(50, 'ETH', '0x1234567890123456789012345678901234567890', 'callback.php');
echo "\nPayout Result:\n";
print_r($payoutResult);
