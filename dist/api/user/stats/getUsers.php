<?php
header("Content-Type:application/json");


session_start();

if (isset($_SESSION['logged_in']) and $_SESSION['is_admin']) {

    include('../../dbh.api.php');


    $sql = "SELECT id, name, surname, email, timestamp FROM `user`";
    $result = mysqli_query($conn, $sql);

    $users = array();

	if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    
        $data = array();
    
        $data["user_count"] = mysqli_num_rows($result);
        $data["users"] = $users;

        response("Request succesfull", $data);
    } else response("Request failed", "Php/db error");
} else response("Request failed", "Not logged in / not admin");




function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}