<?php
header("Content-Type:application/json");



session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in'])) {
    include('../../dbh.api.php');
    
    $ads = false;
    if (!empty($_POST['ads'])) $ads = $_POST['ads'];



    $email = $_SESSION['email'];
    
    $sql = "UPDATE user SET 
        ads = '$ads'
        WHERE email='$email'";
    if (mysqli_query($conn, $sql)) response("Request succesfull", "Data updated");
    else response("Request failed", "Error updating record");  //mysqli_error($conn);
        
} else response("Request failed", "Not logged in");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}