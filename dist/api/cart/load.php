<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");

if (isset($_SESSION['logged_in'])) {
    if (empty($_SESSION['cart'])) $_SESSION['cart'] = array();
    if (empty($_SESSION['cart_real_meta'])) $_SESSION['cart_real_meta'] = array();
    response("Request fullfiled", [$_SESSION['cart'], $_SESSION['cart_real_meta']]);
} else response("Request failed", "Not logged in");

function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
?>