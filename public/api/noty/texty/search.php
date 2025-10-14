<?php
header("Content-Type:application/json");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

include('../../dbh.api.php');

$search = "";
if (!empty($_GET['search'])) $search = $_GET['search'];
$schvalene = "true";
if (!empty($_GET['schvalene'])) $schvalene = $_GET['schvalene'];
$kategoria = "";
if (!empty($_GET['kategoria'])) $kategoria = $_GET['kategoria'];

$pagination_index = 0;
if (!empty($_GET['pagination_index'])) $pagination_index = $_GET['pagination_index'];
$pagination_limit = 100;
if (!empty($_GET['pagination_limit'])) $pagination_limit = $_GET['pagination_limit'];


$sql = "SELECT id, nazov, autor FROM `texty` WHERE nazov LIKE '%$search%' and schvalene='$schvalene'";
if ($kategoria) $sql .= " and kategoria='$kategoria'";
$sql .= " ORDER BY `oblubene` DESC";
$result = mysqli_query($conn, $sql);

$data = array();

$data_n = 0;
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $data[$data_n] = $row;
        $data_n++;
    }
}

$r = $data;

$data_count = count($data);
$p_i = 0;

while($p_i < $pagination_index and $p_i < $data_count) {
    if (array_key_exists($p_i, $r)) unset($r[$p_i]);
    $p_i++;
}


$p_i = $p_i + $pagination_limit;

while($p_i < $data_count) {
    //echo $p_i;
    if (array_key_exists($p_i, $r)) unset($r[$p_i]);
    $p_i++;
}

/*


$p_l = count($r);
while($p_l > $pagination_limit) {
    $r = array_pop($r);
    $p_l--;
}
*/

response("Request succesfull", $r);





function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
