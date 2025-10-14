<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");


if (isset($_SESSION['logged_in'])) {

    if (!isset($_SESSION['delivery'])) $_SESSION['delivery'] = "0";
    if (!isset($_SESSION['cashOnDelivery'])) $_SESSION['cashOnDelivery'] = "false";


    if (isset($_GET['delivery'])) { //id
        $_SESSION['delivery'] = $_GET['delivery'];
    }
    if (isset($_GET['cashOnDelivery'])) { // true/false
        $_SESSION['cashOnDelivery'] = $_GET['cashOnDelivery'];
    }

    $r = [
        "delivery" => $_SESSION['delivery'],
        "cashOnDelivery" => $_SESSION['cashOnDelivery']
    ];
    response("Request fullfiled", $r);
} else response("Request failed", "Not logged in");




function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
