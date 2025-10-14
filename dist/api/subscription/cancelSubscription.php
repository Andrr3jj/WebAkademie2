<?php

require_once str_replace("subscription", "cart", __DIR__) . '/GoPay/vendor/autoload.php';

use GoPay\Definition\Language;
use GoPay\Definition\Payment\Currency;
use GoPay\Definition\Payment\PaymentInstrument;
use GoPay\Definition\Payment\BankSwiftCode;
//use GoPay\Definition\Payment\Recurrence;



header("Content-Type:application/json");

session_start();
//include("../antispam.php");


if (isset($_SESSION['logged_in'])) {
    
    $user_id = $_SESSION['id'];
    
    $time_now = time();
    
    include('../dbh.api.php');
    $sql = "SELECT * FROM subscription WHERE user_id='$user_id' AND duration>'$time_now' AND gopay_status='PAID' AND recurent=true AND initial=true ORDER BY timestamp ASC";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $id = $row['id'];
        }
    
        $sql = "UPDATE subscription SET recurent=false WHERE id='$id'";

        if (mysqli_query($conn, $sql)) response("Request succesfull");
        else response("Request failed", "Php/db error");
    } else response("Request failed", "Subscription not found");
} else response("Request failed", "Not logged in");



function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
