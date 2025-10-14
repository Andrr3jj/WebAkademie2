<?php
header("Content-Type:application/json");

response("Request sucesfull", json_encode($_POST['input']));

function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
