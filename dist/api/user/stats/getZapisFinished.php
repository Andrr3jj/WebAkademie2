<?php
header("Content-Type:application/json");


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();


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

function is_owned($product_id) {
    global $conn;
    $user_id = $_SESSION['id'];
    $products_owned = "";
    $sql = "SELECT productsOwned FROM `user` WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            $products_owned = $row['productsOwned'];
        }
    }
    if (!empty($products_owned)) {
        if (str_contains($products_owned, $product_id)) return true;
    }
    return false;
}

if (isset($_SESSION['logged_in'])) {
    include('../../dbh.api.php');

    $user_id = $_SESSION['id'];

    //$sql = "SELECT products FROM `order` WHERE user_id='$user_id'";
    $sql = "SELECT product_id FROM `product_finished` WHERE user_id='$user_id' AND finished=true";
    $result = mysqli_query($conn, $sql);

    $count = 0;

	if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $product = $row['product_id'];
            if ($product > 3) {
                $product_data = getProductData($product);
                if ($product_data != $product) {
                    if (json_decode($product_data['details'], true)['typ'] == "Zapis") {
                        if (is_owned($product)) $count++;
                        //$sql = "SELECT finished FROM `product_finished` WHERE user_id='$user_id' AND product_id='$product' AND finished=true";
                        //$result = mysqli_query($conn, $sql);      
                        //echo $sql;          
                        //if (mysqli_num_rows($result) > 0) $count++;
                    }
                }
            }
        }
        response("Request succesfull", $count);
    } else response("Request failed", "Php/db error");
} else response("Request failed", "Not logged in");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
