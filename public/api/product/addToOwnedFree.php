<?php

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
    if (isset($_GET['user_id']) and !empty($_GET['product_id'])) {

        $user_id = $_GET['user_id'];
        $product_id = $_GET['product_id'];
        include('../dbh.api.php');
        global $conn;
        if (addToOwned($user_id, $product_id)) response("Request succesfull");
        else response("Request failed", "PHP/DB error");
    } else response("Request failed", "Empty params");
} else response("Request failed", "Not logged in / not admin / roles mixmatch");


function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
