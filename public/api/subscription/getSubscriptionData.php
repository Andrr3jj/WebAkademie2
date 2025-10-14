<?php

header("Content-Type:application/json");

session_start();
include("../antispam.php");
if (isset($_SESSION['logged_in'])) {
    include('../dbh.api.php');


    $user_id = $_SESSION['id'];

    $time_now = time();

    $data = array();
    $sql = "SELECT * FROM subscription WHERE user_id='$user_id' AND duration>'$time_now' AND gopay_status='PAID' ORDER BY timestamp ASC";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $subscription = $row;
        }
    }

    $cancel_id = $subscription['parent_id'];
    if ($cancel_id == 0) $cancel_id = $subscription['id'];
    
    $data = [
        "subscription_type" => $subscription['name'],
        "subscription_valid_from" => strtotime($subscription['timestamp']),
        "subscription_valid_to" => $subscription['duration'],
        "subscription_is_recurent" => $subscription['recurent'],
        "subscription_cancel_id" => $cancel_id
    ];
    response("Request succesfull", $data);

} else response("Request failed", "Not logged in");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
