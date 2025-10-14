<?php
header("Content-Type:application/json");

session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in'])) {
    if ($_SESSION['is_admin'] ) response("Request fullfiled", "Is logged in as admin");
    else response("Request fullfiled", "Is logged in");  
} else response("Request fullfiled", "Not logged in");

function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}