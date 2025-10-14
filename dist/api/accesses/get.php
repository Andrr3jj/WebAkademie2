<?php
header("Content-Type:application/json");

session_start();
include('../dbh.api.php');

function is_access($access) {
    global $conn;
    $user_id = $_SESSION['id'];
    $accesses = "";
    $sql = "SELECT accesses FROM `user` WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            $accesses = $row['accesses'];
        }
    }
    if (!empty($accesses)) if (str_contains($accesses, $access)) return true;
    return false;
}

if (!isset($_SESSION['logged_in']) ) response("Request failed", "Not logged in");
if (empty($_GET['access'])) response("Request failed", "access not provided");
$access_requested = $_GET['access'];
$is_access = is_access($access_requested);
if ($is_access or $_SESSION['is_admin']) {
    include $access_requested . ".php";
    $data = json_decode($data);
    response("Request succesfull", $data);
}
response("Request failed", "No access");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
    die();
}
