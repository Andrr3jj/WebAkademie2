<?php
header("Content-Type:application/json");

session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in'])) {
    include('../../dbh.api.php');
    
    $product_id = $_GET['id'];
    $id = $_SESSION['id'];

    $sql = "SELECT finished FROM product_finished WHERE user_id='$id' AND product_id='$product_id' AND finished=true";
    $result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) == 1) {
        response("Request succesfull", "True");
    } else response("Request succesfull", "False");

} else {
    response("Request failed", "Not logged in");
}



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}