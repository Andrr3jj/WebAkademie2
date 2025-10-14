<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function get_product_price($product_id) {
    global $conn;

    $sql = "SELECT price FROM `product` WHERE virtuality='true' AND id='$product_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            return $row['price'];
        }
    }
    return false;
}
