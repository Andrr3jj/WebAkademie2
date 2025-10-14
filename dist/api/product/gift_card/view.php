<?php
header("Content-Type:application/json");

session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true and !empty($_GET['code'])) {
    include('../../dbh.api.php');
    $code = $_GET['code'];
    $sql = "SELECT * FROM gift_card WHERE code='$code'";
    $data = array();
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
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
