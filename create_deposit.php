<?php
require_once __DIR__ . '/src/CoinsbuyAPI.php';

$api = new CoinsbuyAPI(true); // Use true for sandbox testing

$depositData = [
    'data' => [
        'type' => 'deposit',
        'attributes' => [
            'label' => 'you-domain-name Test Deposit',
            'tracking_id' => 'ye-' . time(),
            'confirmations_needed' => 2,
            'callback_url' => 'https://you-domain-name.com/coinsbuy-callbacks/callback.php',
            'payment_page_redirect_url' => 'https://you-domain-name.com',
            'payment_page_button_text' => 'Return to you-domain-name'
        ],
        'relationships' => [
            'wallet' => [
                'data' => [
                    'type' => 'wallet',
                    'id' => '798' // Replace with your actual wallet ID
                ]
            ]
        ]
    ]
];

try {
    $depositResult = $api->createDeposit($depositData);
    echo "Deposit Creation Result:\n";
    print_r($depositResult);

    if (isset($depositResult['errors'])) {
        echo "Error creating deposit:\n";
        print_r($depositResult['errors']);
    } elseif (isset($depositResult['data']['id'])) {
        echo "Deposit created successfully. Deposit ID: " . $depositResult['data']['id'] . "\n";
    }
} catch (Exception $e) {
    echo "Exception occurred: " . $e->getMessage() . "\n";
}