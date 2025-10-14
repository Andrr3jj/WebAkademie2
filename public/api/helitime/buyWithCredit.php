<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");

session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/get_credit.php";
include "./services/get_product_price.php";
include "./services/buy_with_credit.php";

if (!is_logged_in()) response("Request failed", "Not logged in");
if (empty($_GET['product_id'])) response("Request failed", "product id not provided");
$product_id = $_GET['product_id'];

$user_id = -1;
$config = json_decode(file_get_contents("./config/helitime.config.json"), true);
$helitime_to_eur_value_multiplier = $config['helitime_to_eur_value_multiplier'];
$credit = get_credit($user_id);
$credit = $credit[array_key_first($credit)];
$credit *= $helitime_to_eur_value_multiplier;
$product_price = get_product_price($product_id);
$product_price *= 100;
if (!$product_price) response("Request failed", "Product not found");
if ($credit < $product_price) response("Request failed", "Not enought credit");

if (buy_with_credit($product_id)) response("Request succesfull");
response("Request failed");
