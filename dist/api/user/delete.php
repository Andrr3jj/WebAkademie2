<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");
if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true) {
    if (!empty($_GET['email'])) {

        //if (!empty($_POST['id'])) $id = $_POST['id'];
        //if (!empty($_GET['id'])) $id = $_GET['id'];
        $email = $_GET['email'];

        include('../dbh.api.php');


        $sql = "DELETE FROM user WHERE email='$email'";
        if (mysqli_query($conn, $sql)) response("Request fullfiled", "Data updated");
        else response("Request failed", "Error updating record");  //mysqli_error($conn);
    } else response("Invalid request");
}else response("Request failed", "Not logged in");


function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}