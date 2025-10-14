<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type:application/json");

include('../../dbh.api.php');

function generateNumber() {
    $nums = "123456789";
    return $nums[mt_rand(0, strlen($nums)-1)];
}

function generateCharacter() {
    $chars = "ABCDEFGHIJKLMNPRSTUVWXYZ";
    return $chars[mt_rand(0, strlen($chars)-1)];
}

function generateCode($code = "") {
    //X00X-XX0X-00XX 
    global $conn;
    
    $arr = [];
    $sql = "SELECT code FROM gift_card";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) while($row = mysqli_fetch_assoc($result)) $arr[] = $row["code"];
    
    if (in_array($code, $arr) and $code != "") return false;
    
    
    $res = $code;

    //while (strlen($res) < 14) {
        switch (strlen($res)) { //easily replaceable with elseif chain
            case 0: $res .= generateCharacter(); 
            case 1: $res .= generateNumber();
            case 2: $res .= generateNumber();
            case 3: $res .= generateCharacter();
            case 4: $res .= "-";
            case 5: $res .= generateCharacter();
            case 6: $res .= generateCharacter();
            case 7: $res .= generateNumber();
            case 8: $res .= generateCharacter();
            case 9: $res .= "-";
            case 10: $res .= generateNumber();
            case 11: $res .= generateNumber();
            case 12: $res .= generateCharacter();
            case 13: $res .= generateCharacter();
        }
    //}

    if (in_array($res, $arr))  return generateCode($code);
    return $res;
}




session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true and in_array("coupon_master", $_SESSION['roles'])) {
    $code = "";
    if (!empty($_POST['code'])) $code = $_POST['code'];
    $code = generateCode($code);
    $value = 0;
    if (!empty($_POST['value'])) $value = $_POST['value'];
    $value_original = $value;
    if (!empty($_POST['value_original'])) $value_original = $_POST['value_original'];
    $giftable_products = [];
    if (!empty($_POST['giftable_products'])) $giftable_products = $_POST['giftable_products'];
    $giftable_products = json_encode($giftable_products);
    $giftable_accesses = [];
    if (!empty($_POST['giftable_accesses'])) $giftable_accesses = $_POST['giftable_accesses'];
    $giftable_accesses = json_encode($giftable_accesses);    
    $valid_until = time() + 31556926; // 1 rok
    if (!empty($_POST['valid_until'])) $valid_until = $_POST['valid_until'];

    $sql = "INSERT INTO gift_card (code, value, value_original, giftable_products, giftable_accesses, valid_until, origin)
    VALUES ('$code', '$value', '$value_original' '$giftable_products', '$giftable_accesses', '$valid_until', 'admin')";

    if (mysqli_query($conn, $sql)) response("Request fullfiled", $code);
    else response("Request failed", "Php/db error"); //mysqli_error($conn);
}else response("Request failed", "Not logged in / not admin");

function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
