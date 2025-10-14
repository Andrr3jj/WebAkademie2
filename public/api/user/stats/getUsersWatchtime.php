<?php
header("Content-Type:application/json");

session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in'])) {
    include('../../dbh.api.php');

    $sql = "SELECT user_id, watchtime FROM product_watchtime";
    $result = mysqli_query($conn, $sql);

    $data = array();
    $users_with_watchtime = array();

	if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $watchtime = $row['watchtime'];
            $user_id = $row['user_id'];
            $sql_ = "SELECT name, surname FROM `user` WHERE id='$user_id'";
            $result_ = mysqli_query($conn, $sql_);
            $users = array();
            if (mysqli_num_rows($result_) > 0) {
                while($row_ = mysqli_fetch_assoc($result_)) {
                    if (empty($data[$row_['name'] . " " . $row_['surname']])) $data[$row_['name'] . " " . $row_['surname']] = 0;
                    $data[$row_['name'] . " " . $row_['surname']] += $watchtime;
                    $users_with_watchtime[] = $row_['name'] . " " . $row_['surname']; 
                }
            }
        }
    }

    $sql = "SELECT name, surname FROM `user`";
    $result = mysqli_query($conn, $sql);
    $users = array();
	if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $users[] = $row['name'] . " " . $row['surname'];
        }
    }

    foreach ($users as $user) {
        if (!in_array($user, $users_with_watchtime)) {
            $data[$user] = 0;
        }
    }


    response("Request succesfull", $data);
} else response("Request failed", "Not logged in");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
