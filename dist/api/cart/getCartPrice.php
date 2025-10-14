<?php
header("Content-Type:application/json");
session_start();
//include("../antispam.php");



if (isset($_SESSION['logged_in'])) {
    if (!empty($_SESSION['cart']) or !empty($_SESSION['cart_real_meta'])) {
        include("../dbh.api.php");

        $price_total = 0; // v centoch!
        $gift_card_total_buy = 0;

        $delivery = 0;
        if (isset($_SESSION['delivery'])) $delivery = $_SESSION['delivery'];
        if (empty($_SESSION['cart_real_meta'])) $delivery = 0;
        if ($delivery > 0) {
            $json = json_decode(file_get_contents("../eshop/delivery.json"), true);
            foreach ($json as $j) {
                if ($j["id"] == $delivery) {
                    $name = $j['name'];
                    $price = $j['price'] / 100;
                }
            }
            $price_total += $price;
        } else if (!empty($_SESSION['cart_real_meta'])) {
            response("Request failed", "Delivery method not choosen");
            die();
        }
        
        $cashOnDelivery = "false";
        if (isset($_SESSION['cashOnDelivery'])) $cashOnDelivery = $_SESSION['cashOnDelivery'];
        if (empty($_SESSION['cart_real_meta'])) $cashOnDelivery = 0;
        if ($cashOnDelivery == "true") {
            $price = file_get_contents("../eshop/cashOnDelivery.json") / 100;
            $price_total += $price;
        }

        if (!empty($_SESSION['cart'])) { 

            $cart = $_SESSION['cart'];
            foreach($cart as $product) {
                $sql = "SELECT * FROM product WHERE id='$product' AND deleted='false'";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                    if (empty($_SESSION['cart_discount_coupon'])) $_SESSION['cart_discount_coupon'] = [];
                    if (count($_SESSION['cart_discount_coupon']) == 0) {
                        $price_this = $row['price'];
                        if ($row['discount_active'] == "true") $price_this = ((100 - $row['discount']) / 100) * $price_this;
                        $price_total += $price_this;
                    } else {
                        $discount_thing_added_product = false;
                        foreach ($_SESSION['cart_discount_coupon'] as $coupon) {
                            $coupon = json_decode($coupon, true);
                            if ($coupon['uses_left'] > 0 and $coupon['valid_until'] > time()) {
                                if ($coupon['type'] == "PERCENT_PRODUCT") {
                                    if ($product == $coupon['percent_product']) {
                                        if (!in_array($product, $products)) {
                                            $price_total += ($row['price'] * (1.00/$coupon['value']));
                                            $discount_thing_added_product = true;
                                        }
                                    }
                                }
                            }
                        }
                        if (!$discount_thing_added_product) {
                            $price_this = $row['price'];
                            if ($row['discount_active'] == "true") $price_this = ((100 - $row['discount']) / 100) * $price_this;
                            $price_total += $price_this;
                        }
                    }
                }
            }   else if (str_contains($product, "gift_card")){
                $price_this = str_replace("gift_card", "", $product) / 100;
                if ($price_this > 1) {
                    $gift_card_total_buy += $price_this;
                    $price_total += $price_this;
                }
            }
        }
    }
    $price_total -= $gift_card_total_buy;

    if (!empty($_SESSION['cart_real_meta'])) {
        $cart_r = $_SESSION['cart_real_meta'];
        foreach($cart_r as $product) {
            $product_id = $product[0];
            $sql = "SELECT * FROM product WHERE id='$product_id' AND deleted='false'";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    if (empty($_SESSION['cart_discount_coupon'])) $_SESSION['cart_discount_coupon'] = [];
                    if (count($_SESSION['cart_discount_coupon']) == 0) {
                        $price_this = $row['price'];
                        if ($row['discount_active'] == "true") $price_this = ((100 - $row['discount']) / 100) * $price_this;
                        $price_total += $price_this;
                    } else {
                        $discount_thing_added_product = false;
                        foreach ($_SESSION['cart_discount_coupon'] as $coupon) {
                            $coupon = json_decode($coupon, true);
                            if ($coupon['uses_left'] > 0 and $coupon['valid_until'] > time()) {
                                if ($coupon['type'] == "PERCENT_PRODUCT") {
                                    if ($product_id == $coupon['percent_product']) {
                                        if (!in_array($product_id, $products)) {
                                            $price_total += ($row['price'] * (1.00/$coupon['value']));
                                            $discount_thing_added_product = true;
                                        }
                                    }
                                }
                            }
                        }
                        if (!$discount_thing_added_product) {
                            $price_this = $row['price'];
                            if ($row['discount_active'] == "true") $price_this = ((100 - $row['discount']) / 100) * $price_this;
                            $price_total += $price_this;
                        }
                    }
                }
            }
        }
    }


        foreach ($_SESSION['cart_discount_coupon'] as $coupon) { 
            $coupon = json_decode($coupon, true);
            if ($coupon['uses_left'] > 0 and $coupon['valid_until'] > time()) {
                if ($coupon['type'] == "PRICE") {
                    $price_total -= $coupon['value'];
                }
                if ($coupon['type'] == "PERCENT") {
                    //$price_total *= (1 / $coupon['value']);
                    $price_total -= $price_total * ($coupon['value'] / 100);
                }
            }
        }

        if (empty($_SESSION['cart_gift_card'])) $_SESSION['cart_gift_card'] = [];
        foreach ($_SESSION['cart_gift_card'] as $gift_card) { 
            $gift_card = json_decode($gift_card, true);
            if ($gift_card['valid_until'] > time() and $gift_card['value'] > '0') {
                $gift_card_discount = $gift_card['value'];
                if ($price_total < $gift_card['value']) $gift_card_discount = $price_total;
                $price_total -= $gift_card_discount;
            }
        }
        $price_total += $gift_card_total_buy;




        if ($price_total < 0) $price_total = 0;
        $price_total = round($price_total, 2);
        $price_total *= 100; // premena na centy

        response("Request sucesfull", $price_total);

        } else response("Request failed", "Cart is empty");
    } else response("Request failed", "Not logged in");
    
    
function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}

