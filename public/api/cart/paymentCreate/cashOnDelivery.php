<?php

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
