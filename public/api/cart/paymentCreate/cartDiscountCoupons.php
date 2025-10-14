<?php

foreach ($_SESSION['cart_discount_coupon'] as $coupon) { 
    $coupon = json_decode($coupon, true);
    if ($coupon['uses_left'] > 0 and $coupon['valid_until'] > time() and $coupon['deleted'] == '0') {
        if ($coupon['type'] == "PRICE") {
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
            $price_total -= $price_total * ($coupon['value'] / 100);
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
