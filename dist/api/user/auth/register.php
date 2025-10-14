<?php
header("Content-Type:application/json");
session_start();
include('../../dbh.api.php');
include("../../antispam.php");
include('../../inputVerification.php');
if (isset($_POST['name']) && verifyName($_POST['name'])
&& isset($_POST['surname']) && verifyName($_POST['surname'])
&& isset($_POST['email']) && verifyEmail($_POST['email'])
&& isset($_POST['password']) && verifyName($_POST['password'])
) {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $ads = false;
    if (!empty($_POST['ads'])) $ads = $_POST['ads'];
    
    $state = "";
    if (!empty($_POST['state'])) $state = $_POST['state'];
    
    $invite_code = "";
    if (!empty($_POST['invite_code'])) $invite_code = $_POST['invite_code'];

    $invited_by = "";
    if (!empty($invite_code)) {
        $sql_invited_by = "SELECT id FROM `user` WHERE invite_code='$invite_code'";
        $result = mysqli_query($conn, $sql_invited_by);
        if (mysqli_num_rows($result) == 1) {
            while($row = mysqli_fetch_assoc($result)) {
                $invited_by = $row['id'];
            }
        }
    }
    
    
    $dir = "../../../data/uploads/product/videos/";
    $free_video = "";
    if (is_dir($dir)) {
        $scandir = scandir($dir);
        $free_index = rand(0, count($scandir) - 3);
        $free_video = $scandir[$free_index];
    }


    $upload_dir = "../../../data/uploads/profile_pictures/default/";
    $target_file = "";

    $files = scandir($upload_dir);
    unset($files[0]);
    unset($files[1]);

    if (count($files) > 0) {        
        $image_file = $files[array_rand($files)];
        $target_file = "https://www.heligonkovaakademia.sk/data/uploads/profile_pictures/default/" . $image_file;
    }



    $sql = "SELECT email FROM user WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) { 
        response("Request failed", "Email already registered");
    } else {
        $sql = "INSERT INTO user (name, surname, email, password, free_video, ads, deliveryAddressState, billingAddressState, profilePhotoUrl, invited_by)
        VALUES ('$name', '$surname', '$email', '$password', '$free_video', '$ads', '$state', '$state', '$target_file', '$invited_by')";

        if (mysqli_query($conn, $sql)) {
            include("../../product/addToOwnedAtRegister.php");
            addToOwnedAtRegister($email, $conn);
            include("../../mail/registerSendEmail.php");
            if (sendMail_registerSendEmail($email, $name, $surname) == "Success") response("Request fullfiled");
            else response("Request succesfull", "Email not sent");
        } else {
            response("Request failed"); //mysqli_error($conn);
        }
    }

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
?>