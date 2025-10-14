<?php
header("Content-Type:application/json");

function getProductData($id) {
    global $conn;
    $sql = "SELECT * FROM `product` WHERE id='$id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            return $row;
        }
    }
    return $id;
}


session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in'])) {
    include('../../dbh.api.php');

    $user_id = $_SESSION['id'];

    $data = 0;

    $sql = "SELECT product_id FROM product_watchtime WHERE user_id='$user_id' ORDER BY timestamp ASC"; // ASC because of mysqli_fetch_assoc loop
    $result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            try {
                $product_id = $row['product_id'];
                $product_data = getProductData($product_id);
                if ($product_data != $product_id) {
                    $product_details = $product_data['details'];
                    if (json_decode($product_details, true)['typ'] == "Video") $data = $product_id;
                }
            }
            catch(Exception $e) {
                //$e->getMessage();
            }
        }
        response("Request succesfull", $data);
    } else response("Request failed", "Nothing"); 
} else response("Request failed", "Not logged in");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
