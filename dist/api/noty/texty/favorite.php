<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type:application/json");

session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in'])) {
    if (!empty($_POST['id'])) {
        $id = $_POST['id'];


        include('../../dbh.api.php');
        $sql = "SELECT * FROM texty WHERE id='$id'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $oblubene = $row['oblubene'];
            }
        }

        if (empty($oblubene)) $oblubene = "[]";
        $oblubene = json_decode($oblubene);
        

        $r = "not favorite";
        if (!in_array($_SESSION['id'], $oblubene)) {
            $oblubene[] = $_SESSION['id'];
            $r = "favorite";
        } 
        else unset($oblubene[array_search($_SESSION['id'], $oblubene)]);

        $oblubene = json_encode($oblubene);

        $sql = "UPDATE texty SET 
            oblubene = '$oblubene'
            WHERE id='$id'";
        mysqli_query($conn, $sql);

        response("Request succesfull", $r);

    } else response("Invalid request");
}else response("Request failed", "Not logged in");


function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
