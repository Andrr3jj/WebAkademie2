<?php
header("Content-Type:application/json");

session_start();
include("../../antispam.php");

if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true) {
    $id = $_GET['id'];
    include('../dbh.api.php');
    
    $sql = "SELECT * FROM form";
    $result = mysqli_query($conn, $sql);
    $formRecords = array();
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            array_push($formRecords, $row);
        }
    }
    response("Request succesfull", $formRecords);
} else response("Request failed", "Not logged in / not admin");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
