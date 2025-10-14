<?php
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
    $user_id = $_SESSION['id'];
    include('../../dbh.api.php');
    $sql = "SELECT productsOwned FROM `user` WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);

    $polka = 0;
    $valcik = 0;
    
	if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            foreach (json_decode($row['productsOwned'], true) as $product) {
                if ($product['id'] > 3) {
                    $product_data = getProductData($product['id']);
                    if ($product_data != $product['id']) {
                        //print_r(json_decode($product_data['details'], true)['statistikakategoria']);
                        if (json_decode($product_data['details'], true)['statistikakategoria'] == "polka" and is_owned($product['id'])) $polka++;
                        else if (json_decode($product_data['details'], true)['statistikakategoria'] == "valcik" and is_owned($product['id'])) $valcik++;
                    }
                }
            }
        }
        
        $count = array();
        $count['polka'] = $polka;
        $count['valcik'] = $valcik;
        response("Request succesfull", $count);
    } else response("Request failed", "Php/db error");
} else response("Request failed", "Not logged in");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
