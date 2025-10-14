<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");
if (isset($_SESSION['logged_in']) and !empty($_SESSION['is_admin']) and $_SESSION['is_admin'] == true and in_array("record_edit", $_SESSION['roles'])) {
    if (!empty($_POST['id'])) {

        //if (!empty($_POST['id'])) $id = $_POST['id'];
        //if (!empty($_GET['id'])) $id = $_GET['id'];
        $id = $_POST['id'];

        include('../dbh.api.php');

        $schvalene = "false";
        if (!empty($_POST['schvalene'])) $schvalene = $_POST['schvalene'];


        $data = "";
        if (!empty($_POST['data'])) $data = $_POST['data'];
        $nazov = "";
        if (!empty($_POST['nazov'])) $nazov = $_POST['nazov'];
        $autor = "";
        if (!empty($_POST['autor'])) $autor = $_POST['autor'];
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
        
        
        //$zobrazenia = 0;
        //if (!empty($_POST['zobrazenia'])) $zobrazenia = $_POST['zobrazenia'];

    
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




        $sql = "UPDATE ciselne_zapisy SET 
            data = '$data',
            nazov = '$nazov',
            autor = '$autor',
            pocet_casti = '$pocet_casti',
            stupnica = '$stupnica',
            pocet_dob = '$pocet_dob',
            pocet_riadkov = '$pocet_riadkov',
            text_piesne = '$text_piesne',
            pata = '$pata',
            product_id = '$product_id',
            typ = '$typ',
            zaner = '$zaner',
            povod = '$povod',
            kategoria = '$kategoria',
            oblubene = '$oblubene',
            autorska = '$autorska',
            jeModerna = '$jeModerna'
            WHERE id='$id'";
        if (mysqli_query($conn, $sql)) response("Request fullfiled", "Data updated");
        else response("Request failed", "Error updating record");  //mysqli_error($conn);
    } else response("Invalid request");
}else response("Request failed", "Not logged in");


function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
