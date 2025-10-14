<?php
header("Content-Type:application/json");


include("../../antispam.php");

if (isset($_SESSION['logged_in'])) {
    if (!empty($_GET['id']) and !empty($_GET['rating']) 
    and $_GET['rating'] >= 0 and $_GET['rating'] <= 5) {
        $id = $_GET['id'];
        $user_id = $_SESSION['id'];
        $rating = $_GET['rating'];
        include('../../dbh.api.php');

        $sql = "SELECT id FROM product WHERE id='$id'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 1) {   
            $sql = "SELECT id from product_rating WHERE product_id='$id' and user_id='$user_id'"; 
            $result = mysqli_query($conn, $sql);

            $sql = "REPLACE INTO product_rating('product_id', 'user_id', 'rating') 
                                VALUES('$id', '$user_id', '$rating')";
            if (mysqli_num_rows($result) == 1) {
                while($row = mysqli_fetch_assoc($result)) {
                    $product_rating_id = $row['id'];
                }
                $sql = "REPLACE INTO product_rating('id', 'product_id', 'user_id', 'rating') 
                                VALUES('$product_rating_id', '$id', '$user_id', '$rating')";
            }
            
            if (mysqli_query($conn, $sql)) response("Request succesfull");
            else response("Request failed");            
        } else response("Request failed", "Product not found");
    } else response("Invalid request");
} else response("Request failed", "Not logged in");





function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
