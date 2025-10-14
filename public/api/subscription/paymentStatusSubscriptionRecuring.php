<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once str_replace("subscription", "cart", __DIR__) . '/GoPay/vendor/autoload.php';

use GoPay\Definition\Language;
use GoPay\Definition\Payment\Currency;
use GoPay\Definition\Payment\PaymentInstrument;
use GoPay\Definition\Payment\BankSwiftCode;
//use GoPay\Definition\Payment\Recurrence;



header("Content-Type:application/json");
//include("../antispam.php");
include "../dbh.api.php";
include "../cart/GoPay.conf.php";
$gopay = GoPay\payments([
    'goid' => $GoPay_config['goid'],
    'clientId' => $GoPay_config['clientId'],
    'clientSecret' => $GoPay_config['clientSecret'],
    'gatewayUrl' => $GoPay_config['gatewayUrl'], 
    'language' => Language::SLOVAK
]);


include(__DIR__ . "/subscriptionPrice.conf.php");
$payment = [
    'amount' => $subscription_available['mesiac']['price'],
    'currency' => Currency::EUROS,
    'order_number' => '123456',
    'order_description' => 'Predlženie predplatneho'
];

$time_now = time();
$time_triger = $time_now + 3600;


$sql = "SELECT * FROM subscription WHERE gopay_status<>'CREATED' AND recurent=true AND initial=false AND name='mesiac' ORDER BY timestamp ASC";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $gopay_id = $row['gopay_id'];
        echo "0;";
        
        $response_ = $gopay->getStatus($gopay_id);
        if ($response_->hasSucceed()) {
            $responseBody_ = $response_->json;
            print_r($responseBody_);
            $paymentStatus = $responseBody_['state'];
            $sql_ = "UPDATE `subscription` SET gopay_status='$paymentStatus' WHERE gopay_id='$gopay_id'";
            mysqli_query($conn, $sql_);
            if ($paymentStatus == "PAID") { 
                include "../mail/subscriptionPaymentSuccesfullSendEmail.php";
                sendMail_subscriptionPaymentSuccesfullSendEmail($email, $duration, $invoice_no);
            }
        }
    }
}


function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
