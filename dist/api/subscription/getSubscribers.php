<?php

header("Content-Type:application/json");

session_start();
include("../antispam.php");

function getUserDataSerialized($id) {
    global $conn;
    $sql = "SELECT name, surname, email FROM `user` WHERE id='$id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            return $row;
        }
    }
}

if (isset($_SESSION['logged_in']) and $_SESSION['is_admin']) {
    include('../dbh.api.php');

    $time_now = time();

    $data = [];
    $data_emails = [];

    $sql = "SELECT user_id, name 
            FROM subscription 
            WHERE duration > $time_now AND gopay_status = 'PAID'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $userDataSerialized = getUserDataSerialized($row['user_id']);

            if (!in_array($userDataSerialized['email'], $data_emails, true)) {
                $userDataSerialized['subscription_name'] = $row['name'];
                $data[] = $userDataSerialized;
                $data_emails[] = $userDataSerialized['email'];
            }
        }
    }

    response("Request succesfull", $data);
} else {
    response("Request failed", "Not logged in / not admin");
}




function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
