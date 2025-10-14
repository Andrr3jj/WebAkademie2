<?php
header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include("../dbh.api.php");


$data = [];

$order = [];
$sql_order = "SELECT * FROM `order` WHERE gopay_status='paid' and invoice_no != '0';";
//$sql_order = "SELECT * FROM `order` WHERE gopay_status='paid' and invoice_no != 0;";

$result_order = mysqli_query($conn, $sql_order);
if (mysqli_num_rows($result_order) > 0) {
    while($row = mysqli_fetch_assoc($result_order)) {
        $order[$row['invoice_no']] = $row;
        $data[$row['invoice_no']] = $row;
    }
} 

$subscription = [];
$sql_subscription = "SELECT * FROM `subscription` WHERE gopay_status='paid' and invoice_no != '0';";
//$sql_subscription = "SELECT * FROM subscription WHERE gopay_status='paid' and invoice_no != '0';";

$result_subscription = mysqli_query($conn, $sql_subscription);
if (mysqli_num_rows($result_subscription) > 0) {
    while($row = mysqli_fetch_assoc($result_subscription)) {
        $subscription[str_replace("S", "", $row['invoice_no'])] = $row;
        $data[str_replace("S", "", $row['invoice_no'])] = $row;
    }
} 


ksort($data);

$invoice_template = "Faktura-" . date("Y") . "-";
$i_t = $invoice_template; 

$invoice_count = 1;


foreach ($data as $data_single) {
    $invoice_no_old = $data_single['invoice_no'];
    $invoice_no_new = $i_t . str_pad($invoice_count, 3, '0', STR_PAD_LEFT);

    $sql_update = "UPDATE `order` SET invoice_no = '$invoice_no_new' WHERE invoice_no = '$invoice_no_old'";

    if (str_contains($data_single['invoice_no'], "S")) {
        $invoice_no_new .= "-S";
        $sql_update = "UPDATE `subscription` SET invoice_no = '$invoice_no_new' WHERE invoice_no = '$invoice_no_old'";
    }

    mysqli_query($conn, $sql_update);
    
    echo "<br>" . $invoice_no_old . " updated to: " . $invoice_no_new;
    $invoice_count++;
}

echo "<br> Next: ".$invoice_count;

//echo "<pre>";
//print_r($order);
//print_r($subscription);
//print_r($data);
//echo "</pre>";

*/