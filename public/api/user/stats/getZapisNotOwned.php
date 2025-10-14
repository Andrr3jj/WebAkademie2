<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type:application/json");

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

function is_not_owned($product_id, $user_id) {
    global $conn;
    //$user_id = $_SESSION['id'];
    $products_owned = "";
    $sql = "SELECT productsOwned FROM `user` WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            $products_owned = $row['productsOwned'];
        }
    }
    if (!empty($products_owned)) {
        $products_owned = strval($products_owned);
        $product_id = '"'.strval($product_id);
        if (!str_contains($products_owned, $product_id)){
            return true;
        } 
    }
    return false;
}
 
if (isset($_SESSION['logged_in']) and isset($_SESSION['is_admin']) and !empty($_GET['user_id'])) {
    include('../../dbh.api.php');

    $user_id = $_GET['user_id'];

    //$sql = "SELECT products FROM `order` WHERE user_id='$user_id'";
    $sql = "SELECT * FROM `product`";
    $result = mysqli_query($conn, $sql);

    $products_not_owned = array();
	if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            //foreach (json_decode($row['productsOwned'], true) as $product_info) {
                $product = $row['id'];
                //echo $product;
                if ($product > 3) {
                    $product_data = getProductData($product);
                    if ($product_data != $product) {
                        if (strtolower(json_decode($product_data['details'], true)['typ']) == "zapis" and is_not_owned($product, $user_id)) array_push($products_not_owned, $product);
                    }    
                }
            //}
        }
        response("Request succesfull", $products_not_owned);
    } else response("Request failed", "Php/db error");
} else response("Request failed", "Not logged in");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
