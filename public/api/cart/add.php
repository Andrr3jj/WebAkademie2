<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
header("Content-Type:application/json");
session_start();
include("../antispam.php");
include('../dbh.api.php');

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
    if (!empty($_GET['id']) ) {
        $id = $_GET['id'];

        $cart_real_meta = "";
        if (!empty($_GET['cart_real_meta'])) $cart_real_meta = $_GET['cart_real_meta'];
        if (empty($_SESSION['cart_real_meta'])) $_SESSION['cart_real_meta'] = [];

        $sql = "SELECT id, virtuality FROM product WHERE id='$id' and deleted='false'";
        $result = mysqli_query($conn, $sql);
        
        if (empty($_SESSION['cart'])) $_SESSION['cart'] = array();
	    if (mysqli_num_rows($result) == 1) {
            while($row = mysqli_fetch_assoc($result)) { 
                $virtuality = $row['virtuality'];
            }

            if ((!in_array($id, $_SESSION['cart']) and !is_owned($id)) or $virtuality == "false") {
                if ($virtuality == "true") array_push($_SESSION['cart'], $id);
                else $_SESSION['cart_real_meta'][uniqid()] = [$id, $cart_real_meta];
                response("Request fullfiled");
            } else response("Request failed", "Already in cart/owned");
            
        } else if (str_contains($id, "gift_card")){
            array_push($_SESSION['cart'], $id);
            response("Request fullfiled");
        }else response("Request failed", "Product not found");
    } else response("Invalid request");
} else response("Request failed", "Not logged in");




function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
?>