<?php
header("Content-Type:application/json");

session_start();

include('../dbh.api.php');


if (isset($_SESSION['logged_in'])) {
    $pagination_index = 0;
    if (!empty($_GET['pagination_index'])) $pagination_index = $_GET['pagination_index'];
    $pagination_limit = 20;
    if (!empty($_GET['pagination_limit'])) $pagination_limit = $_GET['pagination_limit'];


    $sql = "SELECT id, nazov, autor, stupnica, product_id, zobrazenia FROM `ciselne_zapisy`";
    $sql .= " LIMIT $pagination_index, $pagination_limit";
    $result = mysqli_query($conn, $sql);

    $data = array();

	if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }

    response("Request succesfull", $data);
} else response("Request failed", "Not logged in / not admin");




function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
