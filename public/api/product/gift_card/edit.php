<?php
header("Content-Type:application/json");

include('../../dbh.api.php');

function get_gift_card($id) {
    global $conn;
    $sql = "SELECT * FROM gift_card WHERE id='$id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) while($row = mysqli_fetch_assoc($result)) return $row;
}

session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true and in_array("coupon_master", $_SESSION['roles']) and !empty($_POST['id'])) {
    $id = $_POST['id'];
    $gift_card = get_gift_card($id);

    $code = $gift_card['code'];
    if (!empty($_POST['code'])) $code = $_POST['code'];
    $value = $gift_card['value'];
    if (!empty($_POST['value'])) $value = $_POST['value'];
    $giftable_products = json_decode($gift_card['giftable_products']);
    if (!empty($_POST['giftable_products'])) $giftable_products = $_POST['giftable_products'];
    $giftable_products = json_encode($giftable_products);
    $giftable_accesses = json_decode($gift_card['giftable_accesses']);
    if (!empty($_POST['giftable_accesses'])) $giftable_accesses = $_POST['giftable_accesses'];
    $giftable_accesses = json_encode($giftable_accesses);    
    $valid_until = $gift_card['valid_until'];
    if (!empty($_POST['valid_until'])) $valid_until = $_POST['valid_until'];

    $sql = "UPDATE gift_card SET
    code='$code', value='$value', giftable_products='$giftable_products', giftable_accesses='$giftable_accesses', valid_until='$valid_until'
    WHERE id='$id'";

    if (mysqli_query($conn, $sql)) response("Request fullfiled", $code);
    else response("Request failed", "Php/db error"); //mysqli_error($conn);
}else response("Request failed", "Not logged in / not admin");

function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
