<?php
header("Content-Type:application/json");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION['logged_in'])) {
    $user_id = $_SESSION['id'];

    $upload_dir = "../../../data/uploads/profile_pictures/";
    $target_file = $upload_dir . uniqid() . basename($_FILES["profilePhoto"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    if ($_FILES["profilePhoto"]["size"] < 20000000) {
        if($imageFileType == "jpg" OR $imageFileType == "png" OR $imageFileType == "jpeg" OR $imageFileType == "gif" ) {
            if (move_uploaded_file($_FILES["profilePhoto"]["tmp_name"], $target_file)) {
            	include('../../dbh.api.php');
                $target_file = "https://www.heligonkovaakademia.sk/data/uploads/profile_pictures/" . str_replace($upload_dir, "", $target_file);
                $sql = "UPDATE user SET profilePhotoUrl='$target_file' WHERE id='$user_id'";
                mysqli_query($conn, $sql);

                response("Request succesfull", $target_file);
            }
            else response("Request failed");
        }
    }

} else response("Request failed", "Not logged in");

function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
?>