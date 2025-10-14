<?php
header("Content-Type:application/json");


session_start();

include('../dbh.api.php');

function getProductName($id) {
    global $conn;
    $sql = "SELECT name FROM `product` WHERE id='$id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            return $row['name'];
        }
    }
    return $id;
}

if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] and in_array("helishop_manager", $_SESSION['roles'])) {
    $sql = "SELECT products FROM `order_real`";
    $result = mysqli_query($conn, $sql);

    $products = array();

	if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            foreach (json_decode($row['products']) as $product) {
                $products[] = $product;
            }
        }
    
        $data = array();
    
        foreach($products as $product) {
            $product_name = getProductName($product);
            if (!in_array($product_name, array_keys($data))) $data[$product_name] = 1;
            else $data[$product_name] = $data[$product_name] + 1;
        } 

        response("Request succesfull", $data);
    } else response("Request failed", "Php/db error");
} else response("Request failed", "Not logged in / not admin");




function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
