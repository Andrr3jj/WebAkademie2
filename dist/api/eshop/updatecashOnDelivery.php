<?php

header("Content-Type:application/json");

session_start();

if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true and in_array("merch_edit", $_SESSION['roles'])) {
    if (isset($_POST['price'])) {
        $price = $_POST['price'];
        $file = fopen("./cashOnDelivery.json", "w");
        if ($file) {
            fwrite($file, $price);
            fclose($file);
            response("Request succesfull");
        } else response("Request failed", "Saving failed");
    } else response("Request failed", "Invalid request");
} else response("Request failed", "Not logged in/roles mixmatch");



function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
