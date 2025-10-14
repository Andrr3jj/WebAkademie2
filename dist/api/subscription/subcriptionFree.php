<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
function addToOwned($user_id, $product_id) {
    global $conn;
    $sql = "SELECT productsOwned FROM user WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        while ($row = mysqli_fetch_assoc($result)) {
            $productsOwned = $row['productsOwned'];
        }
        $productsOwned = json_decode($productsOwned);
        $product_json = array(
            'id'    =>  $product_id,
            'expires'   =>  "never"
        );
        $productsOwned[] = $product_json;            
        $productsOwned = json_encode($productsOwned);
        
        $sql = "UPDATE user SET productsOwned='$productsOwned' WHERE id='$user_id'";
        if ($conn->query($sql) === TRUE) {
          //echo "Record updated successfully";
          return true;
        }                
    }
    return false;
}

header("Content-Type:application/json");

session_start();
if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true and in_array("admin_master", $_SESSION['roles'])) {
    if (isset($_GET['user_id']) and !empty($_GET['duration'])) {

        $user_id = $_GET['user_id'];
        $duration = $_GET['duration'];
        
        $name = $duration;
        $recurent = 0;
        $initial = 1;
        $parent_id = 0;
        $gopay_id = 0;
        $gopay_status = "PAID";
        $gopay_url = "none";
        $invoice_no = 0;
        $invoice_path = "";
        $invoice_data = "";


        include("../dbh.api.php");
        global $conn;
        include(__DIR__ . "/subscriptionPrice.conf.php");
        $duration = time() + $subscription_available[$duration]['duration'];


        $sql = "INSERT INTO `subscription` (user_id, name, duration, recurent, initial, gopay_id, gopay_status, gopay_url, invoice_no, invoice_path, invoice_data)
                VALUES ('$user_id', '$name', '$duration', $recurent, true, '$gopay_id', '$gopay_status', '$gopay_url', '$invoice_no', '$invoice_path', '$invoice_data')";
        if (mysqli_query($conn, $sql)) response("Request succesfull");
        else response("Request failed", "PHP/DB error");
    } else response("Request failed", "Empty params");
} else response("Request failed", "Not logged in / not admin / roles mixmatch");


function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
