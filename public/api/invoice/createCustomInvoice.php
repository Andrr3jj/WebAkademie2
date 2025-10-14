<?php
header("Content-Type:application/json");

session_start();

if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true) {
    if ( !empty($_POST['inputData']) ) {

        include("generateInvoice.php");
        $inputData = json_decode($_POST['inputData']);

        $myfile = fopen("invoice_no.txt", "w+");
        $inputData['invoice_no'] = fgets($myfile) + 1;
        fwrite($myfile, $inputData['invoice_no']);
        fclose($myfile);
        
        generateInvoice($inputData);

        $invoice_path = "https://heligonkovaakademia.sk/api/invoice/invoices/" . $invoice_no;

        response("Request succesfull", $invoice_path);

        mysqli_close($conn);
    }else response("Invalid Request");
}else response("Request failed", "Not logged in / Not admin");

function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
