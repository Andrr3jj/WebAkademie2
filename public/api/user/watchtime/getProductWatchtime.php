<?php
header("Content-Type:application/json");

session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in'])) {
    include('../../dbh.api.php');

    $product_id = $_GET['id'];
    $user_id = $_SESSION['id'];

    $sql = "SELECT watchtime FROM product_watchtime WHERE user_id='$user_id' AND product_id='$product_id'";
    $result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            response("Request succesfull", $row['watchtime']);
        }
    } else response("Request failed", "Watchtime not present / Product not found");
} else response("Request failed", "Not logged in");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
