<?php
header("Content-Type:application/json");

session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in'])) {
    include('../../dbh.api.php');

    $id = $_SESSION['id'];

    $sql = "SELECT product_id FROM product_liked WHERE user_id='$id' AND liked=true";
    $result = mysqli_query($conn, $sql);

    $liked = array();
	if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            array_push($liked, $row['product_id']);
        }
    }
    response("Request succesfull", $liked);

} else {
    response("Request failed", "Not logged in");
}



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}