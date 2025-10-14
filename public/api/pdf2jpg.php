<?php
include "dbh.api.php";

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

function is_owned_path($id, $path) {
    //echo 0;
    global $conn;
    $sql = "SELECT data FROM `product` WHERE id='$id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            $data = $row['data'];
        }
    }
    if (!empty($data)) {
        if (str_contains(strtolower($path), strtolower($data))) return true;
    } 
    return false;
}

function get_path($id) {
    //echo 0;
    global $conn;
    $sql = "SELECT data FROM `product` WHERE id='$id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            return $row['data'];
        }
    }
    if (!empty($data)) {
        if (str_contains(strtolower($path), strtolower($data))) return true;
    } 
    return false;
}

session_start();


if ($_SESSION['logged_in']) {
    if (!empty($_GET['path'] and !empty($_GET['id']))) {
        if (is_owned($_GET['id']) or ($_SESSION['is_admin'] and in_array("product_pass", $_SESSION['roles']))) {
            $path = $_GET['path'];
            $dir = "";
            if (!empty($_GET['dir'])) $dir = $_GET['dir'];
            if (!str_contains($path, "..")) {
                $path = "../data/uploads/product/" . get_path($_GET['id']) . "/" . $path;
                if (file_exists($path)){ //is_owned_path($_GET['id'], $_GET['path']) and 
                    $page = 0;
                    if (!empty($_GET['page'])) $page = $_GET['page'];   
                    $im = new imagick($path.'['.$page.']');
                    $im->setImageFormat('jpg');
                    $im->setImageBackgroundColor('#ffffff');
                    $im->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
                    $im = $im->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
                    header('Content-Type: image/jpeg');
                    echo $im;
                }
            }
        }
    }
}
    
