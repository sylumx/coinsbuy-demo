<?php
require_once __DIR__ . '/src/CoinsbuyAPI.php';

$api = new CoinsbuyAPI(true); // Use true for sandbox testing

// First, let's precalculate the fee
$feeCalculationData = [
    'data' => [
        'type' => 'payout-calculation',
        'attributes' => [
            'amount' => '0.1',
            'to_address' => '2N3Ac2cZzRVoqfJGu1bFaAebq3izTgr1WLv'
        ],
        'relationships' => [
            'wallet' => [
                'data' => [
                    'type' => 'wallet',
                    'id' => '798' // Replace with your actual wallet ID
                ]
            ],
            'currency' => [
                'data' => [
                    'type' => 'currency',
                    'id' => '1000' // BTC
                ]
            ]
        ]
    ]
];

try {
    $feeResult = $api->precalculateFee($feeCalculationData);
    echo "Fee Calculation Result:\n";
    print_r($feeResult);

    if (isset($feeResult['data']['attributes']['fee']['medium'])) {
        $feeAmount = $feeResult['data']['attributes']['fee']['medium'];

        // Now let's create the payout
        $payoutData = [
            'data' => [
                'type' => 'payout',
                'attributes' => [
                    'amount' => '0.1',
                    'fee_amount' => $feeAmount,
                    'address' => '2N3Ac2cZzRVoqfJGu1bFaAebq3izTgr1WLv',
                    'label' => 'you-domain-name Test Payout',
                    'tracking_id' => 'ye-payout-' . time(),
                    'confirmations_needed' => 2,
                    'callback_url' => 'https://you-domain-name.com/coinsbuy-callbacks/callback.php',
                    'travel_rule_info' => [
                        'beneficiary' => [
                            'beneficiaryPersons' => [
                                [
                                    'naturalPerson' => [
                                        'name' => [
                                            [
                                                'nameIdentifier' => [
                                                    [
                                                        'primaryIdentifier' => 'Doe',
                                                        'secondaryIdentifier' => 'John'
                                                    ]
                                                ]
                                            ]
                                        ],
                                        'geographicAddress' => [
                                            [
                                                'country' => 'US',
                                                'addressLine' => [
                                                    '123 Main St, Anytown, AN 12345'
                                                ],
                                                'addressType' => 'HOME'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'relationships' => [
                    'wallet' => [
                        'data' => [
                            'type' => 'wallet',
                            'id' => '798' // Replace with your actual wallet ID
                        ]
                    ],
                    'currency' => [
                        'data' => [
                            'type' => 'currency',
                            'id' => '1000' // BTC
                        ]
                    ]
                ]
            ]
        ];

        $payoutResult = $api->createPayout($payoutData);
        echo "\nPayout Creation Result:\n";
        print_r($payoutResult);

        if (isset($payoutResult['errors'])) {
            echo "Error creating payout:\n";
            print_r($payoutResult['errors']);
        } elseif (isset($payoutResult['data']['id'])) {
            echo "Payout created successfully. Payout ID: " . $payoutResult['data']['id'] . "\n";
        }
    } else {
        echo "Error calculating fee.\n";
    }
} catch (Exception $e) {
    echo "Exception occurred: " . $e->getMessage() . "\n";
}