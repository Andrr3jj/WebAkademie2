<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");

if (isset($_SESSION['logged_in'])) {
    if (!empty($_GET['code']) ) {
        $code = $_GET['code'];
        if (empty($_SESSION['cart_subscription_discount_coupon'])) {
            response("Request failed", "No coupons");
        } else {
            $cart_subscription_discount_coupon_old = $_SESSION['cart_subscription_discount_coupon'];
            $cart_subscription_discount_coupon_new = array();
            foreach ($cart_discount_coupon_old as $cdco ) {
                if (json_decode($cdco, true)['code'] != $code) $cart_subscription_discount_coupon_new[] = $cdco;
            }
            $_SESSION['cart_subscription_discount_coupon'] = $cart_subscription_discount_coupon_new;
            response("Request fullfiled");
        }
    } else response("Invalid Request");
} else response("Request failed", "Not logged in");


function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
?>