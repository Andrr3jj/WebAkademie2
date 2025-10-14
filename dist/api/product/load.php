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

function is_subscriber() {
    global $conn;
    $user_id = $_SESSION['id'];
    $time_now = time();

    $sql = "SELECT * FROM subscription WHERE user_id='$user_id' AND duration>'$time_now' AND gopay_status='PAID'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) return false;
    return true;
}

session_start();
include("../antispam.php");
if (isset($_SESSION['logged_in'])) {
    if (!empty($_GET['id'])) {

        //if (!empty($_POST['id'])) $id = $_POST['id'];
        //if (!empty($_GET['id'])) $id = $_GET['id'];
        $id = $_GET['id'];

        include('../dbh.api.php');
 
        $email = $_SESSION['email'];

        if (is_owned($id) or is_subscriber() or ($_SESSION['is_admin'] and in_array("product_pass", $_SESSION['roles']))) {
            $cislene_zapisy = array();
            $sql_0 = "SELECT id FROM ciselne_zapisy WHERE product_id='$id'";
            $result_0 = mysqli_query($conn, $sql_0);
            if (mysqli_num_rows($result_0) > 0) {
                while($row_0 = mysqli_fetch_assoc($result_0)) {
                     array_push($cislene_zapisy, $row_0['id']);
                }
            } 

            $sql = "SELECT * FROM product WHERE id='$id'";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) == 1) {
                while($row = mysqli_fetch_assoc($result)) {
                    $response = $row;
                    $response['ciselne_zapisy'] = $cislene_zapisy;
                    response("Request succesfull", $response);  
                }
            } else response("Request failed", "Product not found");
        } else response("Request failed", "Product not found in owned");

        //$sql = "SELECT productsOwned FROM user WHERE email='$email'";
        //$result = mysqli_query($conn, $sql);
        //
	    //if (mysqli_num_rows($result) == 1) {
        //    while($row = mysqli_fetch_assoc($result)) {
        //        $productsOwned = $row['productsOwned'];
        //        if ( (!empty($productsOwned) and $productsOwned != "null") or $_SESSION['is_admin'] ) {
//
        //            if ( (in_array($id, json_decode($productsOwned))) or $_SESSION['is_admin']) {
        //                $sql = "SELECT * FROM product WHERE id='$id'";
        //                $result = mysqli_query($conn, $sql);
        //                if (mysqli_num_rows($result) == 1) {
        //                    while($row = mysqli_fetch_assoc($result)) {
        //                        response("Request succesfull", $row);  
        //                    }
        //                } else response("Request failed", "Product not found");
        //            } else response("Request failed", "Product not found in owned");
        //        } else response("Request failed", "Product not found in owned");
  	    //	}
        //} else response("Request failed", "Email not found / Multiple found");
    } else response("Invalid request");
} else {
    response("Request failed", "Not logged in");
}



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}