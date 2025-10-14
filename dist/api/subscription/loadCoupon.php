<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");

if (isset($_SESSION['logged_in'])) {
    if (empty($_SESSION['cart_subscription_discount_coupon'])) $_SESSION['cart_subscription_discount_coupon'] = array();
    response("Request sucessfull", $_SESSION['cart_subscription_discount_coupon']);
} else response("Request failed", "Not logged in");


function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
?>