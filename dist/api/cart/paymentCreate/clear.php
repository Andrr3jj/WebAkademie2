<?php 

//foreach($_SESSION['cart_discount_coupon'] as $coupon) {
//    $coupon = json_decode($coupon, true);
//    $couponCode = $coupon['code'];
//    $uses_left_new = $coupon['uses_left'] -1;
//    $sql = "UPDATE coupon SET used_by='$email', uses_left='$uses_left_new' WHERE code='$couponCode'";
//    mysqli_query($conn, $sql);
//}

$_SESSION['cashOnDelivery'] = "false";
$_SESSION['delivery'] = 0;

$_SESSION['cart'] = array();
$_SESSION['cart_real_meta'] = [];
$_SESSION['cart_discount_coupon'] = [];
$_SESSION['cart_discount_giftCard'] = array();
$_SESSION['cart_gift_card'] = [];
