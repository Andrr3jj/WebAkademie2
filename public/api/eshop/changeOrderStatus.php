<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type:application/json");


include('../dbh.api.php');
function get_user_email($user_id) {
    global $conn;
    $sql = "SELECT email FROM `user` WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            return $row['email'];
        }
    }
    return "none";

}

function get_product_photo($product_id) {
    global $conn;
    $sql = "SELECT data FROM `product` WHERE id='$product_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            $data = "https://heligonkovaakademia.sk/data/uploads/product/";
            $data .= $row['data'] . "/";
            $dir = "../../data/uploads/product/" . $row['data'];
            $data .= scandir($dir)[2];
            return $data;
        }
    }
    return "none";
}

session_start();
include("../antispam.php");
if (isset($_SESSION['logged_in']) and !empty($_SESSION['is_admin']) and $_SESSION['is_admin'] == true and in_array("merch_edit", $_SESSION['roles'])) {
    if (!empty($_POST['id']) and !empty($_POST['status'])) {
        $id = $_POST['id'];


        $status = "";
        if (!empty($_POST['status'])) $status = $_POST['status'];
        
        $status_history = "";
        $sql_h = "SELECT * FROM order_real WHERE id='$id'";
        $result_h = mysqli_query($conn, $sql_h);
        if (mysqli_num_rows($result_h) == 1) {
            while($row_h = mysqli_fetch_assoc($result_h)) {
                $order_real = $row_h;
                $status_history = $row_h['status_history'];
                $email = get_user_email($row_h['user_id']);
            }
        }

        $order_id = $order_real['order_id'];
        $sql_o = "SELECT * FROM `order` WHERE id='$order_id'";
        $result_o = mysqli_query($conn, $sql_o);
        if (mysqli_num_rows($result_o) == 1) {
            while($row_o = mysqli_fetch_assoc($result_o)) {
                $order = $row_o;
            }
        }

        $user_id = $order['user_id'];
        $sql_u = "SELECT * FROM `user` WHERE id='$user_id'";
        $result_u = mysqli_query($conn, $sql_u);
        if (mysqli_num_rows($result_u) == 1) {
            while($row_u = mysqli_fetch_assoc($result_u)) {
                $user = $row_u;
            }
        }


        $status_history .= "|" . time() . "_" . $status;

        $sql = "update order_real set status = '$status', status_history = '$status_history' WHERE id='$id'";
        if (mysqli_query($conn, $sql)) {
            include "../mail/changeStatusSendEmail.php";

            $meno = $user['name'];
            $priezvisko = $user['surname'];
            $cislo_objednavky = $id;
            $datum_objednavky = $order['timestamp'];
            $datum_odoslania = date("j.n.Y");
            $fakturacna_adresa = $order['billingAddress'];
            $dodacia_adresa = $order['deliveryAddress'];
            $sposob_dopravy = json_decode($order['products_detailed'], true)[0]['name'] . " (" . number_format((float)json_decode($order['products_detailed'], true)[0]['price'], 2, ',', '') . "€)";
            $objednany_tovar = json_decode($order['products_detailed'], true);
            $cena_celkom = number_format((float)$order['price']/100, 2, ',', '');




            foreach($objednany_tovar as $otw => $value) {
                $key = (int) filter_var($otw, FILTER_SANITIZE_NUMBER_INT);;
                $objednany_tovar[$otw]['photo'] = get_product_photo($key);
            }


            if (sendMail_changeStatusSendEmail($meno, $priezvisko, $email, $cislo_objednavky, $datum_objednavky, 
            $datum_odoslania, $fakturacna_adresa, $dodacia_adresa, $sposob_dopravy, $objednany_tovar, $cena_celkom) == "Success"){
                    response("Request succesfull", "Data updated - Email sent");   
            } else response("Request succesfull", "Data updated - Email not sent");   
        } else response("Request failed", "Error updating record");  //mysqli_error($conn);
    } else response("Invalid request");
}else response("Request failed", "Not logged in");


function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
