<?php
header("Content-Type:application/json");
error_reporting(0); //TO-DO TODO todo
session_start();
include("../antispam.php");

include('../dbh.api.php');

$virtuality = 'true';
if (!empty($_GET['virtuality'])) $virtuality = $_GET['virtuality'];


$category = "";
if (!empty($_GET['category'])) $category = $_GET['category'];
$category = mysqli_real_escape_string($conn, $category);
$search = "";
if (!empty($_GET['search'])) $search = $_GET['search'];
$search = mysqli_real_escape_string($conn, $search);
$pagination_index = 0;
if (!empty($_GET['pagination_index'])) $pagination_index = $_GET['pagination_index'];
$pagination_index = intval($pagination_index); 
//$pagination_index = mysqli_real_escape_string($conn, $pagination_index);
$pagination_limit = 20;
if (!empty($_GET['pagination_limit'])) $pagination_limit = $_GET['pagination_limit'];
//$pagination_limit = intval($pagination_limit);
//$pagination_limit = mysqli_real_escape_string($conn, $pagination_limit);

$details_key = "";
if (!empty($_GET['details_key'])) $details_key = $_GET['details_key'];
$details_value = "";
if (!empty($_GET['details_value'])) $details_value = $_GET['details_value'];

if ($pagination_limit > 500) $pagination_limit = 500;

$sql = "SELECT id,name, details, timestamp FROM product 
WHERE category != 'hidden' and deleted != 'true' and virtuality = '$virtuality'
and name LIKE '%$search%' ORDER BY timestamp DESC";
if ($category != "" and $category != "hidden") {
    $sql = "SELECT id,name, details, timestamp FROM product 
    WHERE category='$category' and deleted != 'true' 
    and name LIKE '%$search%' ORDER BY timestamp DESC";
}
//echo $sql;
$result = mysqli_query($conn, $sql);
$data = array();
if (mysqli_num_rows($result) > 0) {
	while($row = mysqli_fetch_assoc($result)) {
        //print_r($row['details']);
        if ($details_key != "") {
            //echo json_decode($row['details'], true)[$details_key];
            //echo "<br>";
            //echo json_decode($row['details'], true)[$details_key] == $details_value;
            if (strtolower(json_decode($row['details'], true)[$details_key]) == strtolower($details_value)) $data[] = $row;
        } else $data[] = $row;
	}

    if (count($data) < $pagination_limit) $pagination_limit = count($data);
    $data = array_slice($data, $pagination_index, $pagination_limit);
    response("Request succesfull", $data);
} else response("Request succesfull", "");




function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}