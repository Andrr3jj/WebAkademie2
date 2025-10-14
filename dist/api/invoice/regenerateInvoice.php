<?php

/*
header('Content-type: text/html; charset=utf-8');



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_reporting(-1);


include("../dbh.api.php");

session_start();


function get_user_data($user_id) {
    global $conn;
    $sql = "SELECT * FROM `user` WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            return $row;
        }
    }
}

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

echo count($data);
foreach ($data as $data_single) {
    /*$invoice_no = $data_single['invoice_no'];
    $invoice_date = date("d.m.Y", intval($data_single['timestamp']));
    
    $user_data = get_user_data($data_single['user_id']);
    $customer_name = $user_data['name'] . $user_data['surname'];

    $cu_street = $user_data['billingAddressStreet'];
    $cu_no = $user_data['billingAddressHouseNumber'];
    $cu_city = $user_data['billingAddressCity'];
    $cu_psc = $user_data['billingAddressPostcode'];
    $customer_address = [$cu_street . " " . $cu_no,$cu_psc . " " . $cu_city];

    
    $products = $data_single['products_detailed'];
    
    
    if (str_contains($data_single['invoice_no'], "S")) {
        //$invoice_data = json_encode($invoice_data, JSON_UNESCAPED_UNICODE );
        //$sql_update = "UPDATE `subscription` SET invoice_data = '$invoice_data' WHERE invoice_no = '$invoice_no'";
    } else {
        
        $products = $data_single['products_detailed'];
        $price = $data_single['price'];
        
        $shop_address = "Krasňany 108, 01303 Krasňany, Slovensko";
    
        $invoice_data = 
        ["invoice_no" => "$invoice_no",
        "invoice_date" => "02.08.2024",
    
        "shop_name" => "T&K HEL s. r. o.",
        "shop_address" => $shop_address,
        "shop_ico" => "56540116",
        "shop_dic" => "2122340473",
        
        "customer_name" => $customer_name,
        "customer_address" => $customer_address,
        "products" => $products,
        "price_total" => $price]
        ;
        $invoice_data = json_encode($invoice_data, JSON_UNESCAPED_UNICODE );
        $sql_update = "UPDATE `order` SET invoice_data = '$invoice_data' WHERE invoice_no = '$invoice_no'";
        mysqli_query($conn, $sql_update);
        
    }
    
    */
    /*
    if (str_contains($data_single['invoice_no'], "S")) { 

        //$products = ($data_single['products_detailed']);
        //echo $products;
        //$products = json_decode($products);
        //$price_total = $data_single['price'];


        $invoice_no_old = $data_single['invoice_no'];
        $inputData = $data_single['invoice_data'];
        $inputData = json_decode($inputData, true);
        $inputData['invoice_no'] = $invoice_no_old;
        //$inputData['price_total'] = $inputData['price_total']/100;
        //echo $inputData . "|<br><br>";
        //echo "<br><br>||";
        //echo($inputData);
        //echo "<br><br>::";
        //$inputData = explode(',"products":', $inputData)[0] . "}";
        //echo 0 . $inputData . "<br><br>"; 
        //$inputData = json_decode($inputData, true);
        //$inputData['products'] =json_decode($products, true);
        //$inputData['price_total'] = $price_total;
        //print_r($inputData);
        //echo "<br>end<br><br></r></be>";
        //$inputData['invoice_no'] = $invoice_no_old;
        echo "<br>" . $invoice_no_old . ": ";
        //print_r($inputData);
        //if (generateInvoice($inputData)) echo "ok";
        //if ($invoice_no_old == "Faktura-2024-002") {
        
        if (!in_array($invoice_no_old, $_SESSION['done'])) {
            include("./generateInvoice.php");
            generateInvoice($inputData);
            array_push($_SESSION['done'], $invoice_no_old);
        }

        //}

        //print_r($inputData);

        //$invoice_data = json_encode($inputData, JSON_UNESCAPED_UNICODE );
        //$sql_update = "UPDATE `order` SET invoice_data = '$invoice_data' WHERE invoice_no = '$invoice_no_old'";
        //echo "<br><br>";
        //echo $sql_update;
        //echo "<br><br>";
        //echo "<br><br>";
        //mysqli_query($conn, $sql_update);
    }
    
    
    
    $invoice_count++;
}
//echo $invoice_count;
*/


/*
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
foreach ($data as $data_single) {
    $invoice_no = $data_single['invoice_no'];
    $invoice_path = $data_single['invoice_path'];
    if (str_contains($invoice_path, "/api/invoice/invoices/")) $invoice_path = str_replace("/api/invoice/invoices/", "/data/uploads/invoices/", $invoice_path);
    //echo $invoice_path . "<br>";
    
    if (str_contains($data_single['invoice_no'], "S")) $sql_update = "UPDATE `subscription` SET invoice_path = '$invoice_path' WHERE invoice_no = '$invoice_no'";
    else $sql_update = "UPDATE `order` SET invoice_path = '$invoice_path' WHERE invoice_no = '$invoice_no'";
    echo $sql_update . "<br>";
    
    mysqli_query($conn, $sql_update);

}*/

?>