<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");
if (isset($_SESSION['logged_in'])) {
    if (!empty($_POST['id'])) {
        $id = $_POST['id'];
        $views = 0;

        include('../dbh.api.php');
        $sql = "SELECT zobrazenia FROM ciselne_zapisy WHERE id='$id'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $views = $row['views'];
            }
        }



        $sql = "UPDATE ciselne_zapisy SET 
            zobrazenia = '$views'
            WHERE id='$id'";
        if (mysqli_query($conn, $sql)) response("Request fullfiled", "Data updated");
        else response("Request failed", "Error updating record");  //mysqli_error($conn);
    } else response("Invalid request");
}else response("Request failed", "Not logged in");


function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
