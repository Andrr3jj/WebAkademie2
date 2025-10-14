<?php
header("Content-Type:application/json");

session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true) {
    include('../../dbh.api.php');
    $sql = "SELECT * FROM coupon_subscription WHERE deleted=false";
    $data = array();
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    response("Request succesfull", $data);
} else response("Request failed", "Not logged in / not admin");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
