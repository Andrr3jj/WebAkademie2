<?php
header("Content-Type:application/json");

session_start();
include("../../antispam.php");



if ( !empty($_POST['recoveryKey']) and !empty($_POST['newPassword'])) {
    $recoveryKey = $_POST['recoveryKey'];
    $newPassword = md5($_POST['newPassword']);

	include('../../dbh.api.php');


    $sql = "SELECT password FROM user WHERE recoveryKey='$recoveryKey'";
    $result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) == 1) {
  		while($row = mysqli_fetch_assoc($result)) {
    		if ($row['password'] == $newPassword) response("Request failed", "New password is same as old");
			else {
                $sql = "UPDATE user SET 
                    password = '$newPassword',
                    recoveryKey = ''
                    WHERE recoveryKey='$recoveryKey'";
                if (mysqli_query($conn, $sql)) response("Request succesfull", "Data updated");
			}
  		}
    } else response("Request failed", "recoveryKey not found");

    mysqli_close($conn);
}else{
	response("Invalid Request");
}

function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}