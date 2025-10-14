<?php

header("Content-Type:application/json");

session_start();

if (isset($_SESSION['logged_in'])) { 
    $user_id = $_SESSION['id'];
    $invite_code = "";
    
    include('../../dbh.api.php');
    $sql = "SELECT id, email, invite_code FROM `user` WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            $invite_code = $row['invite_code'];
            if ($invite_code == "") {
                $invite_code = md5($row['id'] . $row['email']);
                $sql_update = "UPDATE `user` SET invite_code='$invite_code' WHERE id='$user_id'";
                mysqli_query($conn, $sql_update);
            }
            response("Request succesfull", $invite_code);
        }
    }
}

function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
