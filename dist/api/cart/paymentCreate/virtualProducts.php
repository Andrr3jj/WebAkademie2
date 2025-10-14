<?php 

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
                                        $price_total += ($row['price'] * (1.00/$coupon['value'])); // god knows if this works
                                        array_push($products, $product);
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
                        $product_invoice = [
                            "name" => $row['name'],
                            "qty" => 1,
                            "price" => $price_this
                        ];
                        array_push($products_invoice, $product_invoice);
                    }
                }
            }
        } else if (str_contains($product, "gift_card")){
            $price_this = str_replace("gift_card", "", $product) / 100;
            if ($price_this > 1) {
                $price_total += $price_this;
                $gift_card_total_buy += $price_this;
                array_push($products, $product);
                $product_invoice = [
                    "name" => "Darčekový kupón (" . $price_this . "€)",
                    "qty" => 1,
                    "price" => $price_this
                ];
                array_push($products_invoice, $product_invoice);
            }
        }
    }
}
