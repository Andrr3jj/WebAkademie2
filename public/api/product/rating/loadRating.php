<?php
header("Content-Type:application/json");


include("../../antispam.php");

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    include('../../dbh.api.php');
    
    $sql = "SELECT rating FROM product_rating WHERE product_id='$id'";
    $result = mysqli_query($conn, $sql);
    $number_of_results = mysqli_num_rows($result);
    if ($number_of_results > 0) {
        $rating = 0;
        while($row = mysqli_fetch_assoc($result)) {
            $rating += $row['rating']; 
        }
        response("Request succesfull", $rating/$number_of_results);
    } else response("Request failed", "Product not found / No rating yet");
} else response("Invalid request");

    




function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
