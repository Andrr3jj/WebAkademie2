<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");
if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true) {
    if (!empty($_GET['id'])) {
        $roles_match = false;
        
        //if (!empty($_POST['id'])) $id = $_POST['id'];
        //if (!empty($_GET['id'])) $id = $_GET['id'];
        $id = $_GET['id'];
        $permanent = false;
        if (!empty($_GET['permanent'])) $permanent = $_GET['permanent'];

        include('../dbh.api.php');

        $sql = "SELECT details FROM `product` WHERE id='$id'";
        $result = mysqli_query($conn, $sql);
    
        $data = array();
    
        if (mysqli_num_rows($result) > 0) {
            $sql = "UPDATE product SET 
            deleted = 'true'
            WHERE id='$id'";

            while($row = mysqli_fetch_assoc($result)) {
                $typ_ = strtolower(json_decode($row['details'], true)['typ']);
                //if ( strtolower(json_decode($row['details'], true)['typ']) == "video") {
                //    $sql = "DELETE FROM product WHERE id='$id'";
                //}     
            }

            if ($permanent) $sql = "DELETE FROM product WHERE id='$id'";
            
            
            if ($id < 4 and in_array("subscription_edit", $_SESSION['roles'])) $roles_match = true;
            
            if ($typ_ == "video" and in_array("video_delete", $_SESSION['roles']))$roles_match = true;
            if ($typ_ == "zapis" and in_array("numericRecord_delete", $_SESSION['roles']))$roles_match = true;
            if ($typ_ == "merch" and in_array("merch_delete", $_SESSION['roles']))$roles_match = true;

            if ($roles_match) {
                if (mysqli_query($conn, $sql)) response("Request fullfiled", "Data updated");
                else response("Request failed", "Error updating record");  //mysqli_error($conn);
            } else response("Request failed", "Roles mixmatch");
        } else response("Product not found");
    } else response("Invalid request");
}else response("Request failed", "Not logged in");


function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}