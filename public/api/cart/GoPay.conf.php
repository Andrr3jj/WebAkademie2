<?php

if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
    die();
    //die ("<h2>Access Denied!</h2>");
}

$GoPay_config = [
    'goid' => '8476009806',
    'clientId' => '1332118436',
    'clientSecret' => 'Kuzx8Rvb',
    'gatewayUrl' => 'https://gate.gopay.cz/api/',

    'callback' =>  [
        'return_url' =>  'https://heligonkovaakademia.sk/',
        'notification_url' =>  'https://heligonkovaakademia.sk/api/cart/paymentStatus.php'
    ],

    'callback_subscription' =>  [
        'return_url' =>  'https://heligonkovaakademia.sk/',
        'notification_url' =>  'https://heligonkovaakademia.sk/api/subscription/paymentStatusSubscription.php'
    ],

    'callback_subscription_recuring' =>  [
        'return_url' =>  'https://heligonkovaakademia.sk/',
        'notification_url' =>  'https://heligonkovaakademia.sk/api/subscription/paymentStatusSubscriptionRecuring.php'
    ]
];        

/*
14.03.2025
    'goid' => '8476009806',
    'clientId' => '1332118436',
    'clientSecret' => 'Kuzx8Rvb',

xx.xx.xxxx
    Test SecureKey: LHEcbta7eNWPVyKKBWumFsHV
    Test uživatelské jméno: testUser8747675475
    Test heslo: P2437933
    'goid' => '8728313748',
    'clientId' => '1744908968',
    'clientSecret' => 'bPvTWJGG',
*/
