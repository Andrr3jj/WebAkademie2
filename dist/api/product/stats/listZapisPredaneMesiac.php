<?php
header("Content-Type:application/json");


function is_zapis($id) {
    global $conn;
    $sql = "SELECT * FROM product WHERE id='$id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (strtolower(json_decode($row['details'], true)['typ']) == "zapis") return true;
        }
    }
    return false;
}

session_start();

include('../../dbh.api.php');


if (isset($_SESSION['logged_in']) and $_SESSION['is_admin']) {
    if (empty($_GET['id'])) {
        response("Request failed", "Id not provided");
        die();
    }
    $id = $_GET['id'];

    $year = date("Y");
    if (!empty($_GET['year'])) $year = $_GET['year'];
    $month = date("n");
    if (!empty($_GET['month'])) $month = $_GET['month'];

    $sql = "SELECT * FROM `order` WHERE gopay_status = 'PAID' and products LIKE '%$id%'
    AND YEAR(timestamp) = '$year'
    AND MONTH(timestamp) = '$month'";
    $result = mysqli_query($conn, $sql);
    $data = array();

	if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            if (is_zapis($id)) {
                $data[] = $row;
            }
        }
    }

    $r = count($data);
    response("Request succesfull", $r);
} else response("Request failed", "Not logged in / not admin");




function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
