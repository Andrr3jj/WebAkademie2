<?php
header("Content-Type:application/json");

session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in'])) {
    if (!empty($_POST['newPassword']) AND !empty($_POST['oldPassword'])) {        
        $email = $_SESSION['email'];
        
        $oldPassword = md5($_POST['oldPassword']);
        $newPassword = md5($_POST['newPassword']);
        
        include('../../dbh.api.php');
        
        $sql = "SELECT password FROM user WHERE email='$email'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 1) {
            while($row = mysqli_fetch_assoc($result)) {
                $actualPassword = $row['password'];
            }

            if ($oldPassword == $actualPassword) {
                $sql = "UPDATE user SET password='$newPassword' WHERE email='$email'";

                if (mysqli_query($conn, $sql)) response("Request succesfull");
                else response("Request failed", "PHP/db error");
            } else response("Request failed", "Wrong password");
        } else response("Request failed", "User not found");
    } else response("Invalid request");
} else response("Request failed", "Not logged in");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
