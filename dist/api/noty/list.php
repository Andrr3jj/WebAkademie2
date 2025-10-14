<?php
header("Content-Type:application/json");

session_start();

include('../dbh.api.php');


if (isset($_SESSION['logged_in']) and $_SESSION['is_admin']) {
    $sql = "SELECT id, nazov, autor, stupnica, product_id, timestamp FROM `ciselne_zapisy`";
    $result = mysqli_query($conn, $sql);

    $data = array();

	if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $row['stav_aktivne'] = $row['product_id'] > 0 ? true : false;
            //unset($row['product_id']);
            $row['priečinok'] = ['NotImplemented'];
            $data[] = $row;
        }
    }

    response("Request succesfull", $data);
} else response("Request failed", "Not logged in / not admin");




function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
