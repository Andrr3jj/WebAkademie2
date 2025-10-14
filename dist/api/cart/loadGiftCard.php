<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");

if (isset($_SESSION['logged_in'])) {
    if (empty($_SESSION['cart_gift_card'])) $_SESSION['cart_gift_card'] = array();
    response("Request sucessfull", $_SESSION['cart_gift_card']);
} else response("Request failed", "Not logged in");


function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
?>