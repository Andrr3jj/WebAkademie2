<?php
header("Content-Type:application/json");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include('../dbh.api.php');

$latest_x = 10;
if (!empty($_GET['latest_x'])) $latest_x = $_GET['latest_x'];

$data = [];
$sql = "SELECT id, timestamp, name, additional_text, category, details FROM product ORDER BY id DESC LIMIT $latest_x ";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
} 
response("Request succesfull", $data);




function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
