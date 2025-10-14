<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");
if (!empty($_GET['id'])) {
    //if (!empty($_POST['id'])) $id = $_POST['id'];
    //if (!empty($_GET['id'])) $id = $_GET['id'];
    $id = $_GET['id'];
    if ($_SESSION['is_admin'] and in_array("invoice_master", $_SESSION['roles'])) {
        $filename = "../../data/uploads/invoices/" . $id . ".pdf";
        header("Content-type: application/pdf");
        header("Content-Length: " . filesize($filename));
        // Send the file to the browser.
        readfile($filename);
    } else response("Request failed", "Roles mixmatch");
} else response("Invalid request");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}