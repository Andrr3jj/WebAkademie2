<?php

header("Content-Type:application/json");

session_start();
include("../antispam.php");
if (isset($_SESSION['logged_in']) and $_SESSION['is_admin']) {
    include('../dbh.api.php');

    $time_now = time();

    $data = array();


    $spolu = 0;
    $mesiac = 0;
    $polrok = 0;
    $rok = 0;

    $sql = "SELECT * FROM subscription WHERE duration>'$time_now' AND gopay_status='PAID' and recurent=false";
    $result = mysqli_query($conn, $sql);

    $spolu = mysqli_num_rows($result) > 0;
    if (mysqli_num_rows($result) > 0) {
        $data_user_ids = [];
        while($row = mysqli_fetch_assoc($result)) {
            if ($row['name'] == "mesiac" and !in_array($row['user_id'], $data_user_ids)) $mesiac++;
            if ($row['name'] == "pol rok" and !in_array($row['user_id'], $data_user_ids)) $polrok++;
            if ($row['name'] == "rok" and !in_array($row['user_id'], $data_user_ids)) $rok++;
            $data_user_ids[] = $row['user_id'];
            
        }
    }

    if ($spolu == false) $spolu = 0;

    $data['Predplatne - neopakujuce sa'] = [
        'spolu' => $spolu,
        'mesacne' => $mesiac,
        'pol rocne' => $polrok, 
        'rocne' => $rok
    ];

   
    $spolu = 0;
    $mesiac = 0;
    $polrok = 0;
    $rok = 0;

    $sql = "SELECT * FROM subscription WHERE duration>'$time_now' AND gopay_status='PAID' and recurent=true and initial=true";
    $result = mysqli_query($conn, $sql);

    $spolu = mysqli_num_rows($result) > 0;
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            if ($row['name'] == "mesiac") $mesiac++;
            if ($row['name'] == "pol rok") $polrok++;
            if ($row['name'] == "rok") $rok++;
        }
    }

    if ($spolu == false) $spolu = 0;

    $data['Predplatne - opakujuce sa'] = [
        'spolu' => $spolu,
        'mesacne' => $mesiac,
        'pol rocne' => $polrok, 
        'rocne' => $rok
    ];

    //$cancel_id = $subscription['parent_id'];
    //if ($cancel_id == 0) $cancel_id = $subscription['id'];

    response("Request succesfull", $data);

} else response("Request failed", "Not logged in / not admin");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
