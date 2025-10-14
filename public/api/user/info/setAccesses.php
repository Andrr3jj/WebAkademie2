<?php
header("Content-Type:application/json");

session_start();
if (isset($_SESSION['logged_in'])) {
    if (!empty($_GET['id'])) { //in_array("admin_master", $_SESSION['roles'])
        include('../../dbh.api.php');



        $id = $_GET['id'];

        $accesses = "[]";
        if (!empty($_GET['accesses'])) $accesses = $_GET['accesses'];

        $sql = "UPDATE user SET 
            accesses = '$accesses'
            WHERE id='$id'";
        if (mysqli_query($conn, $sql)) response("Request succesfull", "Data updated");
        else response("Request failed", "Error updating record");  //mysqli_error($conn);
    } else response("Request failed", "Invalid request");
} else response("Request failed", "Not logged in");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
