<?php
header("Content-Type:application/json");

session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in'])) {
    if (in_array("admin_master", $_SESSION['roles']) ) {
        include('../../dbh.api.php');
        
        $roles = "[]";
        if (!empty($_POST['roles'])) $roles = $_POST['roles'];



        $email = $_POST['email'];
        
        $sql = "UPDATE user SET 
            roles = '$roles'
            WHERE email='$email'";
        if (mysqli_query($conn, $sql)) response("Request succesfull", "Data updated");
        else response("Request failed", "Error updating record");  //mysqli_error($conn);
    } else response("Request failed", "Roles mixmatch");
} else response("Request failed", "Not logged in");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}