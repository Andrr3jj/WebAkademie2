<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type:application/json");

session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in'])) {
    $id = $_SESSION['id'];
    if (!empty($_POST['id'])) $id = $_POST['id'];
    include('../../dbh.api.php');
    $search = '"%'.$_SESSION['id'].'%"';
    $sql = "SELECT id FROM texty WHERE oblubene LIKE $search";
    $r = [];
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $r[] = $row;
        }
    }


    response("Request succesfull", $r);

}else response("Request failed", "Not logged in");


function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
