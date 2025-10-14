<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");

if (isset($_SESSION['logged_in'])) {
    if (!empty($_GET['id']) ) {
        if (empty($_SESSION['cart']) and empty($_SESSION['cart_real_meta'])) {
            $_SESSION['cart'] = array();
            response("Request failed", "Cart empty");
        } else {
            if (!empty($_SESSION['cart']) and in_array($_GET['id'], $_SESSION['cart'])) {
                if (($key = array_search($_GET['id'], $_SESSION['cart'])) !== false) {
                    unset($_SESSION['cart'][$key]);
                    $new_cart = array();
                    foreach ($_SESSION['cart'] as $cart_item) {
                        array_push($new_cart, $cart_item);
                    }
                    $_SESSION['cart'] = $new_cart;
                    response("Request fullfiled");
                }
            } elseif (!empty($_SESSION['cart_real_meta']) and array_key_exists($_GET['id'], $_SESSION['cart_real_meta'])) {
                unset($_SESSION['cart_real_meta'][$_GET['id']]);
                response("Request fullfiled");
            }
            else response("Request failed", "Not in cart");
        }
    } else response("Invalid Request");
} else response("Request failed", "Not logged in");


function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
?>
