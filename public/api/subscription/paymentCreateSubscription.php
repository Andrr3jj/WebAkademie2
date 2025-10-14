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

session_start();
//include("../antispam.php");



if (isset($_SESSION['logged_in'])) {
    if (!empty($_GET['duration'])) {
        $user_id = $_SESSION['id'];
        
        $time_now = time();
        
        include("../dbh.api.php");
        $sql = "SELECT * FROM subscription WHERE user_id='$user_id' AND duration>'$time_now' AND gopay_status='PAID'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 0) {
            $duration = $_GET['duration'];
            $recurent = false;
            //if ($duration == "mesiac" AND !empty($_GET['recurent'])) $recurent = $_GET['recurent'];


            include(__DIR__ . "/subscriptionPrice.conf.php");
            include("../dbh.api.php");

            $order_number = 0;

            $order = $duration;
            $price_total = $subscription_available[$duration]['price']; // v centoch!
            $price_total_product = $subscription_available[$duration]['price']; // v centoch!
            $products_invoice = array(); // moved
            
            
            //cart_subscription_discount_coupon
            if (!empty($_SESSION['cart_subscription_discount_coupon'])) {
                foreach ($_SESSION['cart_subscription_discount_coupon'] as $coupon) { 
                    $coupon = json_decode($coupon, true);
                    if ($coupon['uses_left'] > 0 and $coupon['valid_until'] > time()) {
                        if ($coupon['type'] == "PRICE") {
                            $price_total -= $coupon['value'];
                            $product_invoice = [
                                "name" => $coupon['code'],
                                "qty" => 1,
                                "price" => $coupon['value']
                            ];
                            array_push($products_invoice, $product_invoice);
                        }
                        if ($coupon['type'] == "PERCENT") {
                                ///$price_total = ($price_total * ((100 - $coupon['value'])/100));
                                ///$price_total *= (100 - $coupon['value'])/100;
                            $coupon_value = -1 * round($price_total * ($coupon['value'] / 100), 2);
                            //$price_total -= $price_total * ($coupon['value'] / 100);
                            $price_total = $price_total * ((100 - $coupon['value'])/100);
                                ///$price_total = $price_total * (1 / $coupon['value'] * 10);
                            $product_invoice = [
                                "name" => $coupon['code'],
                                "qty" => 1,
                                "price" => $coupon_value/100
                            ];
                            array_push($products_invoice, $product_invoice);
                        }
                    }
                }
            }




            $duration = time() + $subscription_available[$duration]['duration'];

            $products = array();
            array_push($products, "subscription_".$duration);

            $product_invoice = [
                "name" => "Predplatné " . $order,
                "qty" => 1,
                "price" => $price_total_product / 100
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


            include "../cart/GoPay.conf.php";
            $gopay = GoPay\payments([
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
                'callback' =>  $GoPay_config['callback_subscription']
            ];

            //if ($recurent) {
            //    $payment['recurrence'] = [
            //        'recurrence_cycle' => 'ON_DEMAND',
            //        'recurrence_date_to' => '2099-12-31'
            //    ];
            //}


            $response = $gopay->createPayment($payment);

            if ($response->hasSucceed()) {
                $responseBody = $response->json;

                $gopay_id = $responseBody['id'];
                $gopay_status = $responseBody['state'];
                $gopay_url = $responseBody['gw_url'];


                $price_total_sql = $price_total;
                $products_sql = json_encode($products);

                $invoice_no = 0;
                $invoice_path = "";

                $recurent_sql = "false";
                if ($recurent) $recurent_sql = "true";

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

                $inputData = json_encode($inputData, JSON_UNESCAPED_UNICODE);

                $sql = "INSERT INTO `subscription` (user_id, name, duration, recurent, initial, gopay_id, gopay_status, gopay_url, invoice_no, invoice_path, invoice_data)
                VALUES ('$user_id', '$order', '$duration', $recurent_sql, true, '$gopay_id', '$gopay_status', '$gopay_url', '$invoice_no', '$invoice_path', '$inputData')";

                if (mysqli_query($conn, $sql)) {
                        response("Request succesfull", $gopay_url);
                    } else response("Request failed", "Php/db error");
                } else response("Request failed", "GoPay error");
            } else response("Request failed", "Already subscriber");
        } else response("Invalid request", "");
    } else response("Request failed", "Not logged in");
    
    
function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
