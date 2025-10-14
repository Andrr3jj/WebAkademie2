<?php
header("Content-Type:application/json");

session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true and in_array("coupon_master", $_SESSION['roles'])) {
    $type_supported = ['PRICE', 'PERCENT', 'PERCENT_PRODUCT'];
    if (isset($_POST['value'])
    && isset($_POST['type'])
    && in_array($_POST['type'], $type_supported)
    ) {
        $uses_left = 9999999;
        if (!empty($_POST['uses_left'])) $uses_left = $_POST['uses_left'];
        $percent_product = 0;
        if (!empty($_POST['percent_product'])) $percent_product = $_POST['percent_product'];
        $type = $_POST['type'];
        $value = $_POST['value'];
        $code = uniqid();
        if (!empty($_POST['code'])) $code = $_POST['code'];
        
        $valid_until = time();
        if (!empty($_POST['valid_until'])) $valid_until = $_POST['valid_until'];

                
    	include('../../dbh.api.php');

        $sql = "INSERT INTO coupon_subscription (uses_left, type, code, value, percent_product, valid_until)
        VALUES ('$uses_left', '$type', '$code', '$value', '$percent_product', '$valid_until')";

        if (mysqli_query($conn, $sql)) response("Request fullfiled", $code);
        else response("Request failed", "Php/db error"); //mysqli_error($conn);
    }else response("Invalid Request");
}else response("Request failed", "Not logged in / not admin");

function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
