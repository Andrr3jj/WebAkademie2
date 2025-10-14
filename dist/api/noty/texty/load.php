<?php
header("Content-Type:application/json");

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


session_start();
include("../antispam.php");
if (!empty($_GET['id'])) {
    //if (!empty($_POST['id'])) $id = $_POST['id'];
    //if (!empty($_GET['id'])) $id = $_GET['id'];
    $id = $_GET['id'];
    include('../../dbh.api.php');

    $sql = "SELECT * FROM texty WHERE id='$id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            response("Request succesfull", $row);
            //if ($_SESSION['is_admin'] or is_owned($row['product_id'])) response("Request succesfull", $row);  
            //else response("Request failed", "Product not owned");
        }
    } else response("Request failed", "Product not found");
} else response("Invalid request");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
