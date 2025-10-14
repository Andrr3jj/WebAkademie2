<?php
include('../dbh.api.php');

function get_user($user_id) {
    global $conn;
    $sql = "SELECT * FROM `user` WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            return $row;
        }
    }
    return [];

}

function get_order($order_id) {
    global $conn;
    $sql = "SELECT * FROM `order` WHERE id='$order_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            return $row;
        }
    }
    return [];

}

header("Content-Type:application/json");

session_start();

if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true and in_array("helishop_manager", $_SESSION['roles'])) {

    if (!empty($_GET['id']) ) {
        $id = $_GET['id'];

        $data = [];
        $sql = "SELECT * FROM `order_real` WHERE `id`='$id'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 1) {
            while($row = mysqli_fetch_assoc($result)) {

                $data['order_real_id'] = $row['id']; 
                $data['order_id'] = $row['order_id']; 
                $data['user_id'] = $row['user_id']; 
                
                $user = get_user($data['user_id']);
                $order = get_order($data['order_id']);

                $data['user_data'] = [
                    "id" => $user['id'],
                    "name" => $user['name'],
                    "surname" => $user['surname'],
                    "email" => $user['email'],
                    "tel" => $user['phone'],
                    "address" => ""
                        . $user['deliveryAddressStreet'] . " " 
                        . $user['deliveryAddressHouseNumber'] . " "
                        . $user['deliveryAddressPostcode'] . " " 
                        . $user['deliveryAddressCity'],  
                    "billingAddress" => ""
                        . $user['billingAddressStreet'] . " " 
                        . $user['billingAddressHouseNumber'] . " "
                        . $user['billingAddressPostcode'] . " " 
                        . $user['billingAddressCity']  
                ];

                $data['billingAddress'] = $order['billingAddress'];
                $data['deliveryAddress'] = $order['deliveryAddress'];
                
                $data['products_real'] = $row['products']; 
                $data['products_real_cout'] = count(json_decode($row['products'])); 
                $data['products'] = $order['products'];
                $data['products_detailed'] = $order['products_detailed'];
                $data['products_cout'] = count(json_decode($order['products']));

                $data['timestamp'] = $order['timestamp'];
                $data['status'] = $row['status'];

                $data['invoice_link'] = $order['invoice_path'];
                $data['price_total'] = $order['price'];

            }
        }
        
        response("Request succesfull", $data);

        mysqli_close($conn);
    }else response("Invalid Request");
}else response("Request failed", "Not logged in");

function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
