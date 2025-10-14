<?php
header("Content-Type:application/json");

session_start();
include("../../antispam_email.php");

include("../../inputVerification.php");

if ( !empty($_POST['email']) and verifyEmail($_POST['email'])) { 
    $email = $_POST['email'];
    $recoveryKey = time();


    include('../../dbh.api.php');

    $sql = "SELECT email, name, surname FROM user WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) == 1) {
        $name = $row['name'];
        $surname = $row['surname'];
        $sql = "SELECT recoveryKey FROM user WHERE recoveryKey='$recoveryKey'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 0) { 
            $sql = "UPDATE user SET 
            recoveryKey = '$recoveryKey'
            WHERE email='$email'";
            if (mysqli_query($conn, $sql)) {
                include("../../mail/forgotPasswordSendEmail.php");
                if (sendMail_forgotPasswordSendEmail($email, $recoveryKey, $name, $surname) == "Success") response("Request fullfiled", $recoveryKey);
                else response("Request failed", $recoveryKey);
            }
            else response("Request failed", "Error updating record");  //mysqli_error($conn);
        } else response("Request failed", "recoveryKey already in use");
    } else response("Request failed", "Email not found");

} else response("Invalid request");

function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
