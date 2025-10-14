<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");

if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true) {
    if (!empty($_POST['file'])) {
        if (unlink('../../' . $_POST['file'])) response("Request succesfull");
        else response("Request failed");
    }else response("Invalid Request");
}else response("Request failed", "Not logged in / not admin");

function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;

    $json_response = json_encode($response);
    echo $json_response;
}
