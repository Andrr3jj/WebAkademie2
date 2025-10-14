<?php
header("Content-Type:application/json");


session_start();

include('../../dbh.api.php');


if (isset($_SESSION['logged_in']) and $_SESSION['is_admin']) {
    $sql = "SELECT id, name, details, difficulty FROM `product`";
    $result = mysqli_query($conn, $sql);

    $data = array();

	if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            if ( strtolower(json_decode($row['details'], true)['typ']) == "zapis") $data[] = $row;
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
