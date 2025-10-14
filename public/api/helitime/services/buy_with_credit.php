<?php

include_once dirname(__FILE__) . "/../../dbh.api.php";

include_once dirname(__FILE__) . "/get_credit.php";
include_once dirname(__FILE__) . "/get_product_price.php";
include_once dirname(__FILE__) . "/../../product/addToOwned.php";
include_once dirname(__FILE__) . "/get_helitime_history.php";

function buy_with_credit($product_id) {
    global $conn;


    $user_id = -1;
    $user_email = $_SESSION['email'];
    $config = json_decode(file_get_contents("./config/helitime.config.json"), true);
    $helitime_to_eur_value_multiplier = $config['helitime_to_eur_value_multiplier'];
    $credit = get_credit($user_id);
    $credit = $credit[array_key_first($credit)];
    $credit_old = $credit;
    $credit *= $helitime_to_eur_value_multiplier;
    $product_price = get_product_price($product_id);
    $product_price *= 100;

    if (!$product_price or $credit < $product_price) return false;

    $credit_new = $credit - $product_price;
    $credit_new /= $helitime_to_eur_value_multiplier;

    
    $user_id = $_SESSION['id'];
    $helitime_history = get_helitime_history($user_id);
    $helitime_history .= "|" . date("d.m.Y s:i:H") . " -> buy product_id:" . $product_id . " credit_before:" . $credit_old . " credit_after:" . $credit_new;
    $sql = "UPDATE `user` SET credit='$credit_new', helitime_history='$helitime_history' WHERE id='$user_id'";
    $product_id = [$product_id];
    if (addToOwned($user_email, $product_id, $conn) and mysqli_query($conn, $sql)) return true;

    return false;
}
