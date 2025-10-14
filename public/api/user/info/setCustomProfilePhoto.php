<?php
header("Content-Type:application/json");
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

session_start();
if (isset($_SESSION['logged_in'])) {
    if (!empty($_POST['profilePhotoUrl'])) {

        $user_id = $_SESSION['id'];
        $upload_dir = "../../../data/uploads/profile_pictures/default/";
        $target_file = "";
        
        
        include('../../dbh.api.php');
        $sql = "SELECT profilePhotoUrl FROM user WHERE id ='$user_id'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $profilePhotoUrl = $row['profilePhotoUrl'];
            }
        }
        
        
        if (!empty($profilePhotoUrl) and !str_contains($profilePhotoUrl, "default/")) {
            $profilePhotoUrl = str_replace("https://www.heligonkovaakademia.sk", "../../..", $profilePhotoUrl);
            unlink($profilePhotoUrl);
        }
        
        
        $target_file = $_POST['profilePhotoUrl'];
        $sql = "UPDATE user SET profilePhotoUrl='$target_file' WHERE id='$user_id'";
        mysqli_query($conn, $sql);
        
        response("Request succesfull", $target_file);
    } else response("Request failed", "new photo url not provided");
} else response("Request failed", "Not logged in");
    
    function response($status, $data = ""){
        $response['status'] = $status;
        $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
?>