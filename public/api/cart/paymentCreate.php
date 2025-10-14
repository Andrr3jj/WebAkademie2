<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/GoPay/vendor/autoload.php';

use GoPay\Definition\Language;
use GoPay\Definition\Payment\Currency;
use GoPay\Definition\Payment\PaymentInstrument;
use GoPay\Definition\Payment\BankSwiftCode;
//use GoPay\Definition\Payment\Recurrence;



header("Content-Type:application/json");

session_start();
//include("../antispam.php");

$logData = "LOG: ";

$response = "";

if (isset($_SESSION['logged_in'])) {
    if (!empty($_SESSION['cart']) or !empty($_SESSION['cart_real_meta'])) {
        include("../dbh.api.php");

        $order_number = 0;
        $price_total = 0; // v centoch!
        $gift_card_total_buy = 0;




        $products = array();
        $products_invoice = array();


        // pocitanie cien
        include "./paymentCreate/delivery.php"; // doprava
        include "./paymentCreate/cashOnDelivery.php"; // dobierka

        include "./paymentCreate/virtualProducts.php"; // virtualne produkty + prisluchajuce kupony
        $price_total -= $gift_card_total_buy; // vypnutie zlavy pre nove gift_card
        include "./paymentCreate/realProducts.php"; // realne produkty + prisluchajuce kupony
        
        include "./paymentCreate/cartDiscountCoupons.php"; // kupony na cely kosik
        include "./paymentCreate/giftCards.php"; // darčekove poukážky na cely kosik
        $price_total += $gift_card_total_buy; // vypnutie zlavy pre nove gift_card
        
        

        $price_total = round($price_total, 2);
        $price_total *= 100; // premena na centy

        if ($price_total < 0) $price_total = 0;


        $user_id = $_SESSION['id'];

        include "./paymentCreate/userInfo.php"; // nacita fakturacne udaje



        include "GoPay.conf.php";
        $gopay = GoPay\payments([ // prihlasenie
            'goid' => $GoPay_config['goid'],
            'clientId' => $GoPay_config['clientId'],
            'clientSecret' => $GoPay_config['clientSecret'],
            'gatewayUrl' => $GoPay_config['gatewayUrl'],
            'language' => Language::SLOVAK
        ]);


        $payment = [
            'amount' => $price_total,
            'lang' => Language::SLOVAK,
            'currency' => Currency::EUROS,
            'order_number' => $order_number,
            'payer' => [
                'allowed_payment_instruments' => [
                    PaymentInstrument::PAYMENT_CARD,
                    PaymentInstrument::GPAY,
                    PaymentInstrument::APPLE_PAY
                ],
                'default_payment_instrument' => PaymentInstrument::PAYMENT_CARD,
                'contact' => [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'city' => $city,
                    'street' => $street,
                    'postal_code' => $postal_code
                ]
            ],
            'callback' =>  $GoPay_config['callback']
        ];
        

        $response = $gopay->createPayment($payment);

        if (($response->hasSucceed() or $price_total == 0) and !empty($products)) {
            if ($price_total > 0) {
                $responseBody = $response->json;
                
                $gopay_id = $responseBody['id'];
                $gopay_status = $responseBody['state'];
                $gopay_url = $responseBody['gw_url'];
            } else {
                $gopay_id = "(NONE)" . uniqid();
                $gopay_status = "CREATED";
                $gopay_url = "https://heligonkovaakademia.sk/api/cart/paymentStatus.php?id=".$gopay_id;
            }


            $price_total_sql = $price_total;
            $products_sql = json_encode($products);


            $invoice_no = 0;
            $invoice_path = "";

            $products_detailed = json_encode($products_invoice, JSON_UNESCAPED_UNICODE);
            
            $price_total_invoice = $price_total / 100;
            $price_total_invoice =  number_format((float)$price_total_invoice, 2, ',', '');
            
            $invoice_date = date("d.m.Y");
            
            $inputData = [
                "invoice_no" => $invoice_no,
                "invoice_date" => $invoice_date,
            
                "shop_name" => "T&K HEL s. r. o.",
                "shop_address" => "Krasňany 103, 013 03 Krasňany, Slovensko",
                "shop_ico" => "56540116",
                "shop_dic" => "2122340473",
            
                "customer_name" => $first_name . " " . $last_name,
                "customer_address" => [
                    $street,
                    $postal_code . " " . $city,
                    $state
                ],
            
                "products" => $products_invoice,
                "price_total" => $price_total_invoice
            
            
            ];


            $inputData = json_encode($inputData, JSON_UNESCAPED_UNICODE);
             
            $sql = "INSERT INTO `order` (user_id, order_number, products, products_detailed, gift_cards_used, price, gopay_id, gopay_status, gopay_url, invoice_no, invoice_path, invoice_data, deliveryAddress, billingAddress)
            VALUES ('$user_id', '$order_number', '$products_sql', '$products_detailed', '$gift_cards_used', '$price_total_sql', '$gopay_id', '$gopay_status', '$gopay_url', '$invoice_no', '$invoice_path', '$inputData', '$deliveryAddress', '$billingAddress')";
            $logData .=  " sql: " . $sql;


            if (mysqli_query($conn, $sql)) {
                $order_last_id = mysqli_insert_id($conn);


                $products_real = [];

                foreach($products as $products_s) {
                    $sql_l = "SELECT virtuality FROM product WHERE id='$products_s'";
                    $result_l = mysqli_query($conn, $sql_l);

                    if (mysqli_num_rows($result_l) == 1) {
                         while($row = mysqli_fetch_assoc($result_l)) {
                            if ($row['virtuality'] == 'false') $products_real[] = $products_s;
                        }
                    }
                }

                $products_real = json_encode($products_real);
                $sql_08 = "INSERT INTO `order_real` (order_id, user_id, products, status)
                VALUES ('$order_last_id', '$user_id', '$products_real', 'created')";
    
                mysqli_query($conn, $sql_08);

                include "./paymentCreate/clear.php"; // vycistenie sessions kosikov/kuponov...
                response("Request succesfull", $gopay_url);
                $logData .= "Request succesfull " . $gopay_url;
            } else { response("Request failed", "Php/db error"); $logData .= "Php/db error"; }
        } else { response("Request failed", "GoPay error"); $logData .=  "GoPay error" . json_encode($response); }
    } else { response("Request failed", "Cart is empty"); $logData .= "Cart is empty"; }
} else { response("Request failed", "Not logged in"); $logData .= "Not logged in"; }

    
$logData .=  " response: " . json_encode($response);
$logData .= " errors:".json_encode(error_get_last());
$logPath = __DIR__ . "/cart.log";
$mode = (!file_exists($logPath)) ? 'w':'a';
$logfile = fopen($logPath, $mode);
fwrite($logfile, "\r\n". date("Y-m-d H:i:s") ." paymentCreate.php - " . $logData);
fclose($logfile);
    
function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}

