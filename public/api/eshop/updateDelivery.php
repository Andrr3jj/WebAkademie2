<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type:application/json");

session_start();

if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true and in_array("merch_edit", $_SESSION['roles'])) {
    if (isset($_POST['delivery'])) {
        $delivery = $_POST['delivery'];
        if (json_decode($delivery)) {
            $file = fopen("delivery.json", "w");
            if ($file) {
                fwrite($file, $delivery);
                fclose($file);
                response("Request succesfull");
            } else response("Request failed", "Saving failed");
        } else response("Request failed", "Invalid JSON");
    } else response("Request failed", "Invalid request");
} else response("Request failed", "Not logged in/roles mixmatch");



function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
