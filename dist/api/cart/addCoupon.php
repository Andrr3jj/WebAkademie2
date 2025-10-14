<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");


if (isset($_SESSION['logged_in'])) {
    if (!empty($_GET['code']) ) {
        $code = $_GET['code'];
        $time_now = time();

        include('../dbh.api.php');
        
        $sql = "SELECT * FROM coupon WHERE code='$code' AND deleted='0' AND valid_until > '$time_now'";
        $result = mysqli_query($conn, $sql);
        
	    if (mysqli_num_rows($result) == 1) {
            while($row = mysqli_fetch_assoc($result)) {
                if (empty($_SESSION['cart_discount_coupon'])) $_SESSION['cart_discount_coupon'] = array();
                if (!in_array(json_encode($row), $_SESSION['cart_discount_coupon'])) {
                    if ($row['uses_left'] > 0 and $row['deleted'] == false and $row['valid_until'] > time()) {
                        array_push($_SESSION['cart_discount_coupon'], json_encode($row));
                        response("Request fullfiled");
                    } else response("Request failed", "Already used or expired or deleted");
                } else response("Request failed", "Already in cart");
            }
        } else response("Request failed", "Gift card not found / already used");
    } else response("Invalid request");
} else response("Request failed", "Not logged in");




function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
