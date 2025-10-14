<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once str_replace("subscription", "cart", __DIR__) . '/GoPay/vendor/autoload.php';

use GoPay\Definition\Language;
use GoPay\Definition\Payment\Currency;
use GoPay\Definition\Payment\PaymentInstrument;
use GoPay\Definition\Payment\BankSwiftCode;
//use GoPay\Definition\Payment\Recurrence;



header("Content-Type:application/json");

//include("../antispam.php");
include "../dbh.api.php";
include "../cart/GoPay.conf.php";
$gopay = GoPay\payments([
    'goid' => $GoPay_config['goid'],
    'clientId' => $GoPay_config['clientId'],
    'clientSecret' => $GoPay_config['clientSecret'],
    'gatewayUrl' => $GoPay_config['gatewayUrl'], 
    'language' => Language::SLOVAK
]);


include(__DIR__ . "/subscriptionPrice.conf.php");
$payment = [
    'amount' => $subscription_available['mesiac']['price'],
    'currency' => Currency::EUROS,
    'order_number' => '123456',
    'order_description' => 'Test recurrence'
];

$time_now = time();
$time_triger = $time_now + 3600;

$price_total = $subscription_available['mesiac']['price']; // v centoch!
$duration = time() + $subscription_available['mesiac']['duration'];

$sql = "SELECT * FROM subscription WHERE duration<'$time_triger' AND gopay_status='PAID' AND recurent=true AND initial=true AND name='mesiac' ORDER BY timestamp ASC";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $parentId = $row['gopay_id'];
        $response = $gopay->createRecurrence($parentId, $payment);
        if ($response->hasSucceed()) {
            $responseBody = $response->json;
            $gopay_id = $responseBody['id'];
            $gopay_status = $responseBody['state'];
            $gopay_url = $responseBody['gw_url'];

            $filepath = "../invoice/invoice_no.txt";
            $invoice_no = file_get_contents($filepath) + 1;
            $myfile = fopen($filepath, "w+");
            fwrite($myfile, $invoice_no);
            fclose($myfile);

            $invoice_no = "S" . strval(date("Y")) . strval($invoice_no);

            $invoice_path = "https://heligonkovaakademia.sk/api/invoice/invoices/" . $invoice_no;
            
            $user_id = $row['user_id'];

                
                
            include(__DIR__ . "/subscriptionPrice.conf.php");

            $price_total = $subscription_available['mesiac']['price']; // v centoch!
            $duration = time() + $subscription_available['mesiac']['duration'];
            $products = array();
            array_push($products, "subscription_".$duration);
            $products_invoice = array();
            $product_invoice = [
                "name" => "Predplatné " . "mesiac",
                "qty" => 1,
                "price" => $price_total / 100
            ];
            array_push($products_invoice, $product_invoice);

            $sql = "SELECT * FROM user WHERE id='$user_id'";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) == 1) {
                while($row = mysqli_fetch_assoc($result)) {
                    $first_name = $row['name'];
                    $last_name = $row['surname'];
                    $email = $row['email'];
                    $city = $row['billingAddressCity'];
                    $street = $row['billingAddressStreet'] . $row['billingAddressHouseNumber'];
                    $postal_code = $row['billingAddressPostcode'];
                }
            }

            $price_total_sql = $price_total;
            $products_sql = json_encode($products);


            $filepath = "../invoice/invoice_no.txt";
            $invoice_no = file_get_contents($filepath) + 1;
            $myfile = fopen($filepath, "w+");
            fwrite($myfile, $invoice_no);
            fclose($myfile);

            $invoice_no = "S" . strval(date("Y")) . strval($invoice_no);

            $invoice_path = "https://heligonkovaakademia.sk/api/invoice/invoices/" . $invoice_no;


            $sql = "INSERT INTO `subscription` (user_id, name, duration, recurent, initial, gopay_id, gopay_status, gopay_url, invoice_no, invoice_path)
            VALUES ('$user_id', 'mesiac', '$duration', true, false, '$gopay_id', '$gopay_status', '$gopay_url', '$invoice_no', '$invoice_path')";

            if (mysqli_query($conn, $sql)) {
                include "../invoice/generateInvoice.php";

                $price_total_invoice = $price_total / 100;

                $invoice_date = date("d.m.Y");

                $inputData = [
                    "invoice_no" => $invoice_no,
                    "invoice_date" => $invoice_date,

                    "shop_name" => "T&K HEL s. r. o.",
                    "shop_address" => "Krasňany 103, 013 03 Krasňany, Slovensko",
                    "shop_ico" => "56540116",
                    "shop_dic" => "2122340473",

                    "customer_name" => $first_name . $last_name,
                    "customer_address" => [
                        $street,
                        $postal_code . " " . $city
                    ],

                    "products" => $products_invoice,
                    "price_total" => $price_total_invoice


                    ];
                generateInvoice($inputData);

            }
        }
    }
}



function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
