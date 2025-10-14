<?php
header("Content-Type:application/json");

function is_product_real($id) {
    global $conn;

    $sql = "SELECT virtuality FROM product WHERE id='$id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)) {
            if ($row['virtuality'] == 'false') return true;
            else return false;
        }
    }
    
    return true;
}

session_start();
include("../antispam.php");
if (isset($_SESSION['logged_in'])) {
    include('../dbh.api.php');

    $email = $_SESSION['email'];

    $sql = "SELECT productsOwned FROM user WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        while ($row = mysqli_fetch_assoc($result)) {
            $productsOwned = $row['productsOwned'];
        }
        if (empty($productsOwned) || $productsOwned == "null") {
            response("Request succesfull", "{}");
        } else {
            $productsOwned = json_decode($productsOwned);
            $productsOwned_valid = array();
            foreach ($productsOwned as $product) {
                $product_check = json_decode(json_encode($product), true);
                if (($product_check['expires'] == "never") or (strtotime($product_check['expires']) > time()) or $_SESSION['is_admin']) {
                    array_push($productsOwned_valid, $product_check['id']);
                }
            }

            //$productsOwned_valid = json_encode($productsOwned_valid);
            //if ($productsOwned_valid == "[]")  response("Request succesfull", "{}");



            $products_new = array();
            $user_id = $_SESSION['id'];
            $timestamp_r = time() - 604800;

            $sql = "SELECT products FROM `order` WHERE user_id='$user_id' AND gopay_status='PAID' AND timestamp > $timestamp_r";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    foreach (json_decode($row['products'], true) as $p) {
                        if (in_array($p, $productsOwned_valid) and !is_product_real($p)) {
                            array_push($products_new, $p);
                        }
                    }
                }
            }



            response("Request succesfull", $products_new);
        }
    } else response("Request failed", "Email not found / Multiple found");
} else response("Request failed", "Not logged in");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}