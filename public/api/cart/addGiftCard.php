<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");


if (!isset($_SESSION['logged_in'])) response("Request failed", "Not logged in");
if (empty($_GET['code']) ) response("Invalid request");
    
$code = $_GET['code'];
$time_now = time();

include('../dbh.api.php');

$sql = "SELECT * FROM gift_card WHERE code='$code' AND valid_until > '$time_now' AND value>'0'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {
    while($row = mysqli_fetch_assoc($result)) {
        if (empty($_SESSION['cart_gift_card'])) $_SESSION['cart_gift_card'] = array();
        if (!in_array(json_encode($row), $_SESSION['cart_gift_card'])) {
            if ($row['valid_until'] > time() and $row['value'] > '0') {
                array_push($_SESSION['cart_gift_card'], json_encode($row));
                response("Request fullfiled");
            } else response("Request failed", "Already used or expired or deleted");
        } else response("Request failed", "Already in cart");
    }
} else response("Request failed", "Gift card not found / already used");





function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
    die();
}
