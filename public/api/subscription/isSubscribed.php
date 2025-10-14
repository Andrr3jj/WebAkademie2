<?php

header("Content-Type:application/json");

session_start();
include("../antispam.php");
if (isset($_SESSION['logged_in'])) {
    include('../dbh.api.php');


    $user_id = $_SESSION['id'];

    $time_now = time();

    $sql = "SELECT * FROM subscription WHERE user_id='$user_id' AND duration>'$time_now' AND gopay_status='PAID'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) response("Request succesfull", "Is subscribed");
    else response("Request succesfull", "Is not subscribed");

} else response("Request failed", "Not logged in");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
