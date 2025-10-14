<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type:application/json");

session_start();
//include("../antispam.php");




if (isset($_SESSION['logged_in'])) {
    if (!empty($_SESSION['cart']) or !empty($_SESSION['cart_real_meta'])) {
        include("../dbh.api.php");
        $price_discount = 0; // zlava
        $price_total = 0; // v centoch!
        $gift_card_total_buy = 0;
        $gift_card_c_total_buy = 0;

        $gift_card_discount = 0;



        $products = array();
        $products_invoice = array();


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
            array_push($products, "00".$delivery);
            $product_invoice = [
                "name" => $name,
                "qty" => 1,
                "price" => $price
            ];
            array_push($products_invoice, $product_invoice);
        }
        
        $cashOnDelivery = "false";
        if (isset($_SESSION['cashOnDelivery'])) $cashOnDelivery = $_SESSION['cashOnDelivery'];
        if (empty($_SESSION['cart_real_meta'])) $cashOnDelivery = 0;
        if ($cashOnDelivery == "true") {
            $price = file_get_contents("../eshop/cashOnDelivery.json") / 100;
            $price_total += $price;
            array_push($products, "00");
            $product_invoice = [
                "name" => "Dobierka",
                "qty" => 1,
                "price" => $price
            ];
            array_push($products_invoice, $product_invoice);
        }

        if (!empty($_SESSION['cart'])) {
            $cart = $_SESSION['cart'];
            foreach($cart as $product) {
                $sql = "SELECT * FROM product WHERE id='$product' AND deleted='false'";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        if (empty($_SESSION['cart_discount_coupon']) or count($_SESSION['cart_discount_coupon']) == 0) {
                            $price_this = $row['price'];
                            if ($row['discount_active'] == "true") $price_this = ((100 - $row['discount']) / 100) * $price_this;
                            $price_total += $price_this;
                            array_push($products, $product);
                            if (!empty($_SESSION['cart_real_meta']) and array_key_exists($product, $_SESSION['cart_real_meta'])) $row['name'] .= " " . $_SESSION['cart_real_meta'][$product];
                            $product_invoice = [
                                "name" => $row['name'],
                                "qty" => 1,
                                "price" => $price_this
                            ];
                            array_push($products_invoice, $product_invoice);
                            $_SESSION['cart_discount_coupon'] = [];
                        } else {
                            $discount_thing_added_product = false;
                            foreach ($_SESSION['cart_discount_coupon'] as $coupon) {
                                $coupon = json_decode($coupon, true);
                                if ($coupon['uses_left'] > 0 and $coupon['valid_until'] > time()) {
                                    if ($coupon['type'] == "PERCENT_PRODUCT") {
                                        if ($product == $coupon['percent_product']) {
                                            if (!in_array($product, $products)) {
                                                $price_discount += $row['price'] - $row['price'] * (1.00/$coupon['value']);
                                                $price_total += ($row['price'] * (1.00/$coupon['value'])); // god knows if this works
                                                array_push($products, $product);
                                                if (!empty($_SESSION['cart_real_meta']) and array_key_exists($product, $_SESSION['cart_real_meta'])) $row['name'] .= " " . $_SESSION['cart_real_meta'][$product];
                                                $product_invoice = [
                                                    "name" => $row['name'],
                                                    "qty" => 1,
                                                    "price" => $row['price'] * (1.00/$coupon['value'])
                                                ];
                                                array_push($products_invoice, $product_invoice);
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
                                array_push($products, $product);
                                if (!empty($_SESSION['cart_real_meta']) and array_key_exists($product, $_SESSION['cart_real_meta'])) $row['name'] .= " " . $_SESSION['cart_real_meta'][$product];
                                $product_invoice = [
                                    "name" => $row['name'],
                                    "qty" => 1,
                                    "price" => $price_this
                                ];
                                array_push($products_invoice, $product_invoice);
                            }
                        }
                    }
                }   else if (str_contains($product, "gift_card")){
                    $price_this = str_replace("gift_card", "", $product) / 100;
                    if ($price_this > 1) {
                        $price_total += $price_this;
                        $gift_card_total_buy += $price_this;

                    }
                }
            }
        }
        $price_total -= $gift_card_total_buy;


        if (!empty($_SESSION['cart_real_meta'])) {
            $cart_r = $_SESSION['cart_real_meta'];
            foreach($cart_r as $product_r) {
                $product = $product_r[0];
                $sql = "SELECT * FROM product WHERE id='$product' AND deleted='false'";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        if (empty($_SESSION['cart_discount_coupon']) or count($_SESSION['cart_discount_coupon']) == 0) {
                        $price_this = $row['price'];
                        if ($row['discount_active'] == "true") $price_this = ((100 - $row['discount']) / 100) * $price_this;
                        $price_total += $price_this;
                        $_SESSION['cart_discount_coupon'] = [];
                    } else {
                        $discount_thing_added_product = false;
                        foreach ($_SESSION['cart_discount_coupon'] as $coupon) {
                            $coupon = json_decode($coupon, true);
                            if ($coupon['uses_left'] > 0 and $coupon['valid_until'] > time()) { 
                                if ($coupon['type'] == "PERCENT_PRODUCT") {
                                    if ($product == $coupon['percent_product']) {
                                        if (!in_array($product, $products)) {
                                            $price_discount += $row['price'] - $row['price'] * (1.00/$coupon['value']);
                                            $price_total += ($row['price'] * (1.00/$coupon['value'])); // god knows if this works
                                            array_push($products, $product);
                                            if (!empty($_SESSION['cart_real_meta']) and array_key_exists($product, $_SESSION['cart_real_meta'])) $row['name'] .= " " . $_SESSION['cart_real_meta'][$product];
                                            $product_invoice = [
                                                "name" => $row['name'],
                                                "qty" => 1,
                                                "price" => $row['price'] * (1.00/$coupon['value'])
                                            ];
                                            array_push($products_invoice, $product_invoice);
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
                            array_push($products, $product);
                            if (!empty($_SESSION['cart_real_meta']) and array_key_exists($product, $_SESSION['cart_real_meta'])) $row['name'] .= " " . $_SESSION['cart_real_meta'][$product];
                            $product_invoice = [
                                "name" => $row['name'],
                                "qty" => 1,
                                "price" => $price_this
                            ];
                            array_push($products_invoice, $product_invoice);
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
                    $price_discount += $coupon['value'];
                    $price_total -= $coupon['value'];
                    $product_invoice = [
                        "name" => $coupon['code'],
                        "qty" => 1,
                        "price" => $coupon['value']
                    ];
                    array_push($products_invoice, $product_invoice);
                }
                if ($coupon['type'] == "PERCENT") {
                    //$price_total = ($price_total * ((100 - $coupon['value'])/100));
                    //$price_total *= (100 - $coupon['value'])/100;
                    $coupon_value = -1 * round($price_total * ($coupon['value'] / 100), 2);
                    //$price_discount += $price_total * ($coupon['value'] / 100);
                    $price_total -= $price_total * ($coupon['value'] / 100);
                    $price_discount += $coupon_value;
                    //$price_total = $price_total * (1 / $coupon['value'] * 10);
                    $product_invoice = [
                        "name" => $coupon['code'],
                        "qty" => 1,
                        "price" => $coupon_value
                    ];
                    array_push($products_invoice, $product_invoice);
                }
            }
        }


        if (empty($_SESSION['cart_gift_card'])) $_SESSION['cart_gift_card'] = [];
        foreach ($_SESSION['cart_gift_card'] as $gift_card) { 
            $gift_card = json_decode($gift_card, true);

            if ($gift_card['valid_until'] > time() and $gift_card['value'] > '0') {
                $gift_card_discount_this = $gift_card['value'];
                if ($price_total < $gift_card['value']) $gift_card_discount_this = $price_total;
                $price_total -= $gift_card_discount_this;
                $price_discount += $gift_card_discount_this;
                $gift_card_discount -= $gift_card_discount_this;
                
            }
        }
        $price_total += $gift_card_total_buy;


        $price_total = round($price_total, 2);
        //$price_total *= 100; // premena na centy




    if ($price_total < 0) $price_total = 0;



    $coupon_discount = $price_discount + $gift_card_discount;
    
    $price_total_wo = $price_total;
    $price_total_wo -= $coupon_discount;
    $price_total_wo -= $gift_card_discount;
    

    $price_total_invoice = $price_total_wo;
    $price_total_invoice += $coupon_discount;    
    $price_total_invoice += $gift_card_discount;    
    
    
    $gift_card_discount = number_format((float)$gift_card_discount, 2, ',', '');
    $coupon_discount =  number_format((float)$coupon_discount, 2, ',', '');
    $price_total_wo =  number_format((float)$price_total_wo, 2, ',', '');
    $price_total_invoice =  number_format((float)$price_total_invoice, 2, ',', '');
    
    $r = [
        "price_total_w/o_coupon" => $price_total_wo,
        "coupon_discount" => $coupon_discount,
        "gift_card_discount" => $gift_card_discount,
        "price_total" => $price_total_invoice,
    ];
    response("Request succesfull", $r);

    } else response("Request failed", "Cart empty");
} else response("Request failed", "Not logged in");

    

function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}

