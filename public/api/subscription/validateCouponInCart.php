<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");

if (isset($_SESSION['logged_in'])) {
    if (empty($_SESSION['cart_subscription_discount_coupon'])) $_SESSION['cart_subscription_discount_coupon'] = array();
    $count = 0;
    $cart_subscription_discount_coupon_new = array();
    foreach($_SESSION['cart_subscription_discount_coupon'] as $coupon) {
        if (json_decode($coupon, true)['valid_until'] > time()) $cart_subscription_discount_coupon_new[] = $coupon;
        else $count++;
    }
    $_SESSION['cart_subscription_discount_coupon'] = $cart_subscription_discount_coupon_new;
    response("Request sucessfull", "Removed " . $count);
} else response("Request failed", "Not logged in");


function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
?>