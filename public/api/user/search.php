<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");

if (!empty($_SESSION['is_admin'])) {
    include('../dbh.api.php');

    $email = "";
    if (!empty($_GET['email'])) $email = $_GET['email'];

    $sql = "SELECT id, name, surname, email, timestamp FROM user 
    WHERE email LIKE '%$email%'";

    $result = mysqli_query($conn, $sql);
    $data = array();
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
