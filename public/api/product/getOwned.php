<?php
header("Content-Type:application/json");

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
                    array_push($productsOwned_valid, $product);
                }
            }
            //$productsOwned_valid = json_encode($productsOwned_valid);
            if ($productsOwned_valid == "[]")  response("Request succesfull", "{}");
            response("Request succesfull", $productsOwned_valid);
        }
    } else response("Request failed", "Email not found / Multiple found");
} else response("Request failed", "Not logged in");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}