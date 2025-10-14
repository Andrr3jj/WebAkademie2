<?php
header("Content-Type:application/json");

function is_owned($product_id) {
    global $conn;
    $user_id = $_SESSION['id'];
    $products_owned = "";
    $sql = "SELECT productsOwned FROM `user` WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            $products_owned = $row['productsOwned'];
        }
    }
    if (!empty($products_owned)) {
        if (str_contains($products_owned, $product_id)) return true;
    }
    return false;
}

session_start();
include("../dbh.api.php");
include("addToOwned.php");
if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true) { 
    $email = $_SESSION['email'];
    $products = array();
    $sql = "SELECT id FROM product";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            if (!is_owned($row['id'])) array_push($products, $row['id']);
            //print_r($row);
        }
    } 
    addToOwned($email, $products, $conn);
    echo "OK";
}
