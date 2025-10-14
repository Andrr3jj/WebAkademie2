<?php
header("Content-Type:application/json");


session_start();

include('../../dbh.api.php');


if (isset($_SESSION['logged_in'])) {
    $count = 0;
    $sql = "SELECT details FROM `product` WHERE id>3";
    $result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            if (json_decode($row['details'], true)['typ'] == "Video") $count++;;
        }
        response("Request succesfull", $count);
    } else response("Request failed", "Php/db error");
} else response("Request failed", "Not logged in");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
