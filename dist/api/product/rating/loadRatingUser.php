<?php
header("Content-Type:application/json");


include("../../antispam.php");

if (isset($_SESSION['logged_in'])) {
    if (!empty($_GET['id'])) {
        $product_id = $_GET['id'];
        $user_id = $_SESSION['id'];
        include('../../dbh.api.php');
        
        $sql = "SELECT rating FROM product_rating WHERE product_id='$product_id' and user_id='$user_id'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 1) {
            while($row = mysqli_fetch_assoc($result)) {
                response("Request succesfull", $row['rating']);
            }
        } else response("Request failed", "Product not rated yet");
    } else response("Invalid request");
} else response("Request failed", "Not logged in");

    




function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}