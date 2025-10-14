<?php
header("Content-Type:application/json");

session_start();



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

function get_gopay_status($id) {
    global $conn;
    $sql = "SELECT gopay_status FROM `order` WHERE id='$id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            return $row['gopay_status'];
        }
    }
    return "none";

}




if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true and in_array("helishop_manager", $_SESSION['roles'])) {

    if (isset($_POST['from']) && isset($_POST['to']) ) {
        $from = $_POST['from'];
        $to = $_POST['to'];



        $sql = "SELECT * FROM `order_real` WHERE products != '[]' AND `timestamp` > '$from' AND `timestamp` < '$to'";
        $result = mysqli_query($conn, $sql);
        $data = array();
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                //čislo objednavky, email zakaznika, datum, 
                //počet produktov, stav objednavky
                $data_s = [];
                $data_s['id'] = $row['id']; 
                $data_s['order_id'] = $row['order_id']; 
                $data_s['email'] = get_user_email($row['user_id']); 
                $data_s['timestamp'] = $row['timestamp']; 
                $data_s['products_count'] = count(explode(",", $row['products'])); 
                $data_s['status'] = $row['status']; 

                if (get_gopay_status($row['order_id']) == "PAID")$data[] = $data_s;
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
