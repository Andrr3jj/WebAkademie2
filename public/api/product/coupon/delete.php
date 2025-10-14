<?php
header("Content-Type:application/json");

session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true and in_array("coupon_master", $_SESSION['roles'])) {
    if ( isset($_GET['id']) ) {
        $id = $_GET['id'];
    	include('../../dbh.api.php');
        
        $sql = "UPDATE coupon SET deleted=true WHERE id='$id'";
        if (mysqli_query($conn, $sql)) response("Request fullfiled");
        else response("Request failed", "Php/db error"); //mysqli_error($conn);
    }else response("Invalid Request");
}else response("Request failed", "Not logged in / not admin");

function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
