<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");
if (isset($_SESSION['logged_in'])) {

    $schvalene = "false";
    if ($_SESSION['is_admin'] == true) {
        $schvalene = true;
        if (!empty($_POST['schvalene'])) $schvalene = $_POST['schvalene'];
    }

    $data = "";
    if (!empty($_POST['data'])) $data = $_POST['data'];
    $nazov = "";
    if (!empty($_POST['nazov'])) $nazov = $_POST['nazov'];
    $autor = "";
    if (!empty($_POST['autor'])) $autor = $_POST['autor'];
    $autor_id = $_SESSION['id'];
    $pocet_casti = 0;
    if (!empty($_POST['pocet_casti'])) $pocet_casti = $_POST['pocet_casti'];
    $stupnica = "";
    if (!empty($_POST['stupnica'])) $stupnica = $_POST['stupnica'];
    $pocet_dob = 0;
    if (!empty($_POST['pocet_dob'])) $pocet_dob = $_POST['pocet_dob'];
    $pocet_riadkov = 0;
    if (!empty($_POST['pocet_riadkov'])) $pocet_riadkov = $_POST['pocet_riadkov'];
    $text_piesne = "";
    if (!empty($_POST['text_piesne'])) $text_piesne = $_POST['text_piesne'];
    $pata = "";
    if (!empty($_POST['pata'])) $pata = $_POST['pata'];
    $product_id = 0;
    if (!empty($_POST['product_id'])) $product_id = $_POST['product_id'];

    $zobrazenia = 0;

    
    $typ = "";
    if (!empty($_POST['typ'])) $typ = $_POST['typ'];
    $zaner = "";
    if (!empty($_POST['zaner'])) $zaner = $_POST['zaner'];
    $povod = "";
    if (!empty($_POST['povod'])) $povod = $_POST['povod'];
    $kategoria = "";
    if (!empty($_POST['kategoria'])) $kategoria = $_POST['kategoria'];
    $oblubene = "";
    if (!empty($_POST['oblubene'])) $oblubene = $_POST['oblubene'];
    $autorska = "";
    if (!empty($_POST['autorska'])) $autorska = $_POST['autorska'];
    $jeModerna = "";
    if (!empty($_POST['jeModerna'])) $jeModerna = $_POST['jeModerna'];

	include('../dbh.api.php');


    $sql = "INSERT INTO ciselne_zapisy (data, nazov, autor, autor_id, pocet_casti, stupnica, pocet_dob, pocet_riadkov, text_piesne, pata, schvalene, zobrazenia, typ, zaner, povod, kategoria, oblubene, autorska, jeModerna)
    VALUES ('$data', '$nazov', '$autor', '$autor_id','$pocet_casti', '$stupnica', '$pocet_dob', '$pocet_riadkov', '$text_piesne', '$pata', '$schvalene', '$zobrazenia', '$typ', '$zaner', '$povod', '$kategoria', '$oblubene', '$autorska', '$jeModerna')";

    if (mysqli_query($conn, $sql)) {
        response("Request fullfiled");
    } else {
        response("Request failed"); //mysqli_error($conn);
    }
    

    mysqli_close($conn);
    
}else response("Request failed", "Not logged in");

function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
?>