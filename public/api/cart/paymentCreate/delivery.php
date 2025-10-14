<?php

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
} else if (!empty($_SESSION['cart_real_meta'])) {
    response("Request failed", "Delivery method not choosen");
    die();
}
