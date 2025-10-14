<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");
if (isset($_SESSION['logged_in'])) {
    if (!empty($_POST['id'])) {
        $id = $_POST['id'];
        $schvalene = "false";
        $author_id = 0;
        if ($_SESSION['is_admin'] == true) $schvalene = $_POST['schvalene'];
        
        if ($schvalene == "false") die();


        include('../../dbh.api.php');
        $sql = "SELECT * FROM texty WHERE id='$id'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $author_id = $row['author_id'];
            }
        }

        $sql = "UPDATE texty SET 
            schvalene = '$schvalene'
            WHERE id='$id'";
        mysqli_query($conn, $sql);



        $credit = 0;
        $sql = "SELECT credit FROM user WHERE id='$author_id'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $credit = $row['credit'];
            }
        }

        $credit += 10;

        $sql = "UPDATE user SET 
            credit = '$credit'
            WHERE id='$author_id'";
        mysqli_query($conn, $sql);

    } else response("Invalid request");
}else response("Request failed", "Not logged in");


function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
