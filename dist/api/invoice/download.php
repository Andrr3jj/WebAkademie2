<?php

session_start();
include("../dbh.api.php");

if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true) { 
    $year = date("Y");
    if (!empty($_GET['year'])) $year = $_GET['year'];

    $month = false;
    if (!empty($_GET['month'])) $month = $_GET['month'];

    $data = array();

        
    $sql = "SELECT invoice_path, invoice_no FROM `order` WHERE YEAR(timestamp) = '$year'";
    if ($month) $sql .= " AND MONTH(timestamp) = '$month'";
    $sql .= " AND gopay_status='PAID' AND invoice_path !=''";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            array_push($data, $row['invoice_path']);
        }
    } 
    
    $sql = "SELECT invoice_path, invoice_no FROM `subscription` WHERE YEAR(timestamp) = '$year'";
    if ($month) $sql .= " AND MONTH(timestamp) = '$month'";
    $sql .= " AND gopay_status='PAID' AND invoice_path !=''";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            array_push($data, $row['invoice_path']);
        }
    } 
    sort($data, SORT_NATURAL);
    response("Request sucesfull", $data);
}

function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
