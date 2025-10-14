<?php
header("Content-Type:application/json");
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

session_start();
if (isset($_SESSION['logged_in'])) {
    $user_id = $_SESSION['id'];
    $upload_dir = "../../../data/uploads/profile_pictures/default/";
    $target_file = "";



    $files = scandir($upload_dir);
    unset($files[0]);
    unset($files[1]);


    $data = [];
    foreach ($files as $file) {      
        $target_file = "https://www.heligonkovaakademia.sk/data/uploads/profile_pictures/default/" . $file;
        $data[] = $target_file;
    }

    response("Request succesfull", $data);
} else response("Request failed", "Not logged in");

function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
?>