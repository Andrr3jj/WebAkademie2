<?php
header("Content-Type:application/json");

session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in'])) {
    include('../../dbh.api.php');
    
    $product_id = $_GET['id'];
    $user_id = $_SESSION['id'];



    $sql = "SELECT id FROM product WHERE id='$product_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {   
        $timestamp = time();
        $sql = "SELECT id, watchtime from product_watchtime WHERE product_id='$product_id' and user_id='$user_id'"; 
        $result = mysqli_query($conn, $sql);

        $sql = "REPLACE INTO product_watchtime(`product_id`, `user_id`, `watchtime`, `timestamp`) 
                            VALUES('$product_id', '$user_id', '1', '$timestamp')";
        if (mysqli_num_rows($result) == 1) {
            while($row = mysqli_fetch_assoc($result)) {
                $product_watchtime_id = $row['id'];
                $product_watchtime = $row['watchtime'] + 1;
            }
            $sql = "REPLACE INTO product_watchtime(`id`, `product_id`, `user_id`, `watchtime`, `timestamp`) 
                            VALUES('$product_watchtime_id', '$product_id', '$user_id', '$product_watchtime', '$timestamp')";
        }
        
        if (mysqli_query($conn, $sql)) response("Request succesfull");
        else response("Request failed");            
    } else response("Request failed", "Product not found");

} else {
    response("Request failed", "Not logged in");
}



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}






