<?php
header("Content-Type:application/json");

session_start();

include('../../dbh.api.php');



    $pagination_index = 0;
    if (!empty($_GET['pagination_index'])) $pagination_index = $_GET['pagination_index'];
    $pagination_limit = 20;
    if (!empty($_GET['pagination_limit'])) $pagination_limit = $_GET['pagination_limit'];


    $sql = "SELECT id, nazov, autor, stupnica, product_id, zobrazenia, schvalene, oblubene FROM `texty`";
    $sql .= "order by id desc LIMIT $pagination_index, $pagination_limit ";
    $result = mysqli_query($conn, $sql);

$schvalene    = [];
$neschvalene  = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (intval($row['schvalene']) == "1") {
            $schvalene[]   = $row;
        } else {
            $neschvalene[] = $row;
        }
    }
}

    $data = array();

	if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $srdiecka = count(json_decode($row['oblubene']));
            $row['srdiecka'] = $srdiecka;
            $data[] = $row;
        }
    }

    response("Request succesfull", [
    'schvalene'   => $schvalene,
    'neschvalene' => $neschvalene
]);





function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
