<?php

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
                    array_push($products, $product);
                    if (!empty($_SESSION['cart_real_meta']) and array_key_exists($product, $_SESSION['cart_real_meta'])) $row['name'] .= " " . $_SESSION['cart_real_meta'][$product];
                    $product_invoice = [
                        "name" => $row['name'] . $product_r[1],
                        "qty" => 1,
                        "price" => $price_this
                    ];
                    //array_push($products_invoice, $product_invoice);
                    $array_key = $product . $product_r[1];
                    if (!array_key_exists($array_key, $products_invoice)){
                        $products_invoice[$array_key] = $product_invoice;
                    } 
                    else {
                        $product_invoice_old = $products_invoice[$array_key];
                        unset($products_invoice[$array_key]);
        
                        $product_invoice_old['qty'] += 1;
                        $product_invoice_old["price"] += $price_this;
        
                        $products_invoice[$array_key] = $product_invoice_old;
                    }
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
                                        if (!empty($_SESSION['cart_real_meta']) and array_key_exists($product, $_SESSION['cart_real_meta'])) $row['name'] .= " " . $_SESSION['cart_real_meta'][$product];
                                        $product_invoice = [
                                            "name" => $row['name'] . $product_r[1],
                                            "qty" => 1,
                                            "price" => $row['price'] * (1.00/$coupon['value'])
                                        ];
                                        //array_push($products_invoice, $product_invoice);
                                        $array_key = $product . $product_r[1];
                                        if (!array_key_exists($array_key, $products_invoice)){
                                            $products_invoice[$array_key] = $product_invoice;
                                        } 
                                        else {
                                            $product_invoice_old = $products_invoice[$array_key];
                                            unset($products_invoice[$array_key]);
                            
                                            $product_invoice_old['qty'] += 1;
                                            $product_invoice_old["price"] += ($row['price'] * (1.00/$coupon['value']));
                            
                                            $products_invoice[$array_key] = $product_invoice_old;
                                        }
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
                            "name" => $row['name'] . $product_r[1],
                            "qty" => 1,
                            "price" => $price_this
                        ];
                        //array_push($products_invoice, $product_invoice);
                        $array_key = $product . $product_r[1];
                        if (!array_key_exists($array_key, $products_invoice)){
                            $products_invoice[$array_key] = $product_invoice;
                        } 
                        else {
                            $product_invoice_old = $products_invoice[$array_key];
                            unset($products_invoice[$array_key]);
            
                            $product_invoice_old['qty'] += 1;
                            $product_invoice_old["price"] += $price_this;
            
                            $products_invoice[$array_key] = $product_invoice_old;
                        }
                    }
                }
            }
        }
    }
}
