<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type:application/json");
include('../../dbh.api.php');

session_start();

function get_user() {
    global $conn;

    $id = $_SESSION['id'];
    $sql = "SELECT * FROM user WHERE id='$id'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            return $row;
        }
    }
}

function get_gift_card($code) {
    global $conn;

    $sql = "SELECT * FROM gift_card WHERE code='$code'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            return $row;
        }
    }
}

function apply($code, $user, $gift_card) {
    global $conn;
    $user_id = $user['id'];

    if (empty($user['accesses'])) $user['accesses'] = "[]";
    $accesses = json_decode($user['accesses']);
    $accesses_new = json_decode($gift_card['giftable_accesses']);
    foreach ($accesses_new as $a) {
        $accesses[] = $a;
    }
    $accesses = json_encode($accesses);
    
    if (empty($user['productsOwned'])) $user['productsOwned'] = "[]";
    $productsOwned = json_decode($user['productsOwned']);
    $productsOwned_new = json_decode($gift_card['giftable_products']);
    foreach ($productsOwned_new as $p) {
        $product_json = array(
            'id'    =>  $p,
            'expires'   =>  "never"
        );
        $productsOwned[] = $product_json;
    }
    $productsOwned = json_encode($productsOwned);

    $used_by = $gift_card['used_by'] . "|" . date("d.m.Y") . " -> " . $user_id;

    $sql_user = "UPDATE user SET 
        accesses = '$accesses',
        productsOwned = '$productsOwned'
        WHERE id='$user_id'";

    $sql_gift_card = "UPDATE gift_card SET 
        used_by = '$used_by'
        WHERE code='$code'";

    if (mysqli_query($conn, $sql_user) and mysqli_query($conn, $sql_gift_card)) return true;
    return false;
}


if (!isset($_SESSION['logged_in'])) response("Request failed", "Not logged in");
if (empty($_GET['code'])) response("Invalid request", "No code provided");

$code = $_GET['code'];
$gift_card = get_gift_card($code);
if (!$gift_card['valid_until'] > time() or $gift_card['used_by'] != "") response("Request failed", "gift card expired/used");
if (apply($code, get_user(), $gift_card)) response("Request succesfull", $gift_card);
else response("Request failed");




function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
    die();
}
