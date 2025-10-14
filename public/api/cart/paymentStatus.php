<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once __DIR__ . '/GoPay/vendor/autoload.php';

use GoPay\Definition\Language;


$logData = "LOG: ";

if (!empty($_GET['id'])) {

    $gopay_id = $_GET['id'];
    $logData .= "gopay_id: " . $gopay_id;


    include("../dbh.api.php");




    $sql = "SELECT * FROM `order` WHERE gopay_id='$gopay_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        $logData .= "-> valid ";
        while($row = mysqli_fetch_assoc($result)) {
            $order_id = $row['id'];
            $status_old = $row['gopay_status'];
            $products = $row['products'];
            
            $user_id = $row['user_id'];
            $orderNumber = 100 + $row['id'];
            $dateOfOrder = $row['timestamp'];
            $order = $row['products_detailed'];
            $gift_cards_used = $row['gift_cards_used'];
            $orderTotal = $row['price'];
            
            $invoicePath = $row['invoice_path'] . ".pdf";  

            $invoicePath_generate = $row['invoice_no'] . ".pdf";

            $inputData = json_decode($row['invoice_data'], true);


            $billing_address = $row['billingAddress'];
            $delivery_address = $row['deliveryAddress'];

            $sposob_dopravy = "";

            //if (json_decode($order, true)[0]['name']); $sposob_dopravy = json_decode($order, true)[0]['name'] . " (" . number_format((float)json_decode($order, true)[0]['price'], 2, ',', '') . "€)";
            if (str_contains('{"0":{', $order)) {
                if (json_decode($order, true)[0]['name']) $sposob_dopravy = json_decode($order, true)[0]['name'] . " (" . number_format((float)json_decode($order, true)[0]['price'], 2, ',', '') . "€)";
            }
        }

        $sql = "SELECT * FROM `user` WHERE id='$user_id'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 1) {
            while($row = mysqli_fetch_assoc($result)) {
                $email = $row['email'];
                $name = $row['name'];
                $surname = $row['surname'];
                
                
                $city = $row['billingAddressCity'];
                $street = $row['billingAddressStreet'] . $row['billingAddressHouseNumber'];
                $postal_code = $row['billingAddressPostcode'];
                $address = $street . " " . $postal_code . " " . $city;
            }
        }

        /*
        $email, 
        $name, $surname, $address, 
        */

        include "GoPay.conf.php";
        $gopay = GoPay\payments([
            'goid' => $GoPay_config['goid'],
            'clientId' => $GoPay_config['clientId'],
            'clientSecret' => $GoPay_config['clientSecret'],
            'gatewayUrl' => $GoPay_config['gatewayUrl'],
            'language' => Language::SLOVAK
        ]);

        $response = $gopay->getStatus($gopay_id);
        if ($response->hasSucceed() or str_contains($gopay_id, "NONE")) {
            $paymentStatus = "PAID";
            if (!str_contains($gopay_id, "NONE")) {
                $responseBody = $response->json;
                $paymentStatus = $responseBody['state'];
            }
            //$paymentStatus = "PAID"; //
            $logData .= " status_old:".$status_old;
            $logData .= " status_new:".$paymentStatus;
            
            if ($status_old != "PAID" and $status_old != "CANCELED" and $status_old != "TIMEOUTED") {
                $sql_update = "UPDATE `order` SET gopay_status='$paymentStatus' WHERE gopay_id='$gopay_id'";
                if (mysqli_query($conn, $sql_update)) {
                    if ($paymentStatus == "PAID") { 
                        include "../invoice/generateInvoice.php";

                        $filepath = "../invoice/invoice_no.txt";
                        $invoice_no = file_get_contents($filepath) + 1;
                        $myfile = fopen($filepath, "w+");
                        fwrite($myfile, $invoice_no);
                        fclose($myfile);

                        //$invoice_no = date("Y") . strval($invoice_no);
                        $invoice_no = "Faktura-" . date("Y") . "-" . $invoice_no;


                        $invoice_path = "https://heligonkovaakademia.sk/data/uploads/invoices/" . $invoice_no;
                        $invoicePath_generate = $invoice_no . ".pdf";

                        $inputData['invoice_no'] = $invoice_no;

                        generateInvoice($inputData);

                        $sql_update_1 = "UPDATE `order` SET invoice_no='$invoice_no' WHERE gopay_id='$gopay_id'";
                        mysqli_query($conn, $sql_update_1);
                        $sql_update_2 = "UPDATE `order` SET invoice_path='$invoice_path' WHERE gopay_id='$gopay_id'";
                        mysqli_query($conn, $sql_update_2);


                        $new_gift_cards = [];
                        include "./paymentStatus/gift_card_generator.php";
                        $products_t = json_decode($products);
                        foreach ($products_t as $psgfck) {
                            if (str_contains($psgfck, "gift_card")) {
                                $logData .= " product gift card: " . $psgfck . " ";
                                $new_gift_card_code = generateCode();
                                $new_gift_card_value = str_replace("gift_card", "", $psgfck) / 100;
                                $new_gift_card_valid_until = time() + 31556926;
                                $sql_new_gift_card = "INSERT INTO gift_card (code, value, value_original, valid_until, origin)
                                VALUES ('$new_gift_card_code', '$new_gift_card_value', '$new_gift_card_value', '$new_gift_card_valid_until', '$email')";
                                if (mysqli_query($conn, $sql_new_gift_card)) ;
                                $new_gift_card["code"] = $new_gift_card_code;
                                $new_gift_card["value"] = $new_gift_card_value;
                                $new_gift_card["date"] = date("j.n.Y", $new_gift_card_valid_until);
                                $new_gift_cards[] = $new_gift_card;
                            }
                        }
                        $logData .= " gift_cards:".json_encode($new_gift_cards);


                        $gift_cards_used = json_decode($gift_cards_used, true);
                        foreach ($gift_cards_used as $gfu) {
                            $gift_card_code = $gfu['code'];
                            $gift_card_discount = $gfu['gift_card_discount'];
                            $sql_gift_card = "SELECT * FROM `gift_card` WHERE code='$gift_card_code'";
                            $result_gift_card = mysqli_query($conn, $sql_gift_card);
                            if (mysqli_num_rows($result_gift_card) == 1) {
                                while($row_gift_card = mysqli_fetch_assoc($result_gift_card)) {
                                    $gift_card_value = $row_gift_card['value'];
                                 }
                            }
                            $gift_card_value = $gift_card_value * 1.00;
                            $gift_card_value_log_1 = $gift_card_value;
                            //( ( floor($a * 100) - floor($b * 100) ) / 100 );
                            $gift_card_value = ( ( floor($gift_card_value * 100) - floor($gift_card_discount * 100) ) / 100 );
                            $gift_card_value_log_2 = $gift_card_value;
                            //$gift_card_value = $gift_card_value - $gift_card_discount;
                            $used_by = $gfu['used_by'] . "|" . date("d.m.Y") . " uid:" . $user_id . " gcvl1:" . $gift_card_value_log_1 . " gcvl2:" . $gift_card_value_log_2; //gfu == gift_card??
                            $sql_update_gift_card = "UPDATE `gift_card` SET value='$gift_card_value', used_by = '$used_by' WHERE code='$gift_card_code'";
                            mysqli_query($conn, $sql_update_gift_card);
                        } 


                        $logData .= " products:".$products;
                        include("../product/addToOwned.php");
                        if (addToOwned($email, $products, $conn)) {

                            $order_id_real = $order_id;
                            $combined_check = false;
                            $sql_check = "SELECT id FROM order_real WHERE order_id='$order_id'";
                            $result_check = mysqli_query($conn, $sql_check);
                            if (mysqli_num_rows($result_check) > 0) {
                                $combined_check = true;
                                while($row = mysqli_fetch_assoc($result_check)){
                                     $order_id_real = $row['id']; 
                                }
                            }

                            include_once "../mail/paymentSuccesfullSendEmailGiftCard.php";
                            foreach ($new_gift_cards as $ngc) {
                                if (sendMail_paymentSuccesfullSendEmailGiftCard(
                                    $email, $name, $surname, $ngc) == "Success"){
                                        $logData .= " PAID - Email sent (GiftCard)";  
                                } else {
                                    response("Request succesfull", "PAID - Email not sent");   
                                    $logData .= " PAID - Email not sent (GiftCard)";  
                                    $logData .= " DATA: " . $email . $ngc . "|";  
                                }
                            }

                            if ($combined_check) {
                                function get_product_photo($product_id) {
                                    if ($product_id == "Darčeková poukážka") return "https://heligonkovaakademia.sk/data/uploads/images/darcekova_poukazka.png";
                                    global $conn;
                                    $sql = "SELECT data FROM `product` WHERE id='$product_id'";
                                    $result = mysqli_query($conn, $sql);
                                    if (mysqli_num_rows($result) == 1) {
                                        while($row = mysqli_fetch_assoc($result)) {
                                            $data = "https://heligonkovaakademia.sk/data/uploads/product/";
                                            $data .= $row['data'] . "/";
                                            $dir = "../../data/uploads/product/" . $row['data'];
                                            $data .= scandir($dir, SCANDIR_SORT_DESCENDING)[0];
                                            return $data;
                                        }
                                    }
                                    return "none";
                                }
                                $order = json_decode($order, true);
                                foreach($order as $otw => $value) {
                                    $key = (int) filter_var($otw, FILTER_SANITIZE_NUMBER_INT);
                                    if ($key != "") $order[$otw]['photo'] = get_product_photo($key);
                                    else $order[$otw]['photo'] = "default";
                                }
                                $order = json_encode($order);
                                include "../mail/paymentSuccesfullSendEmailCombined.php";
                                if (sendMail_paymentSuccesfullSendEmailCombined(
                                    $email, $order_id_real, $dateOfOrder, $name, $surname, 
                                    $address, $order, $orderTotal, $billing_address, $delivery_address, $sposob_dopravy, $invoicePath_generate) == "Success"){
                                        response("Request succesfull", "PAID - Email sent");   
                                        $logData .= " PAID - Email sent (combined order)";  
                                } else {
                                    response("Request succesfull", "PAID - Email not sent");   
                                    $logData .= " PAID - Email not sent (combined order)";  
                                    $logData .= " DATA: " . $email . $order_id_real . $dateOfOrder . $name . $surname . 
                                    $address . $order . $orderTotal . $billing_address. $delivery_address . $sposob_dopravy . $invoicePath_generate . "|";  
                                }
                            } else {
                                include "../mail/paymentSuccesfullSendEmail.php";
                                if (sendMail_paymentSuccesfullSendEmail(
                                    $email, $orderNumber, $dateOfOrder, $name, $surname, 
                                    $address, $order, $orderTotal, $billing_address, $invoicePath_generate) == "Success"){
                                        response("Request succesfull", "PAID - Email sent");   
                                        $logData .= " PAID - Email sent";  
                                } else {
                                    response("Request succesfull", "PAID - Email not sent");   
                                    $logData .= " PAID - Email not sent";  
                                    $logData .= " DATA: " . $email . $orderNumber . $dateOfOrder . $name . $surname . 
                                    $address . $order . $orderTotal . $billing_address . $invoicePath_generate . "|";  
                                }
                            }
                        } else {
                            response("Request failed", "Php/db error _1 MANUAL");
                            $logData .= " Php/db error _1";  
                        }
                    } else if ($paymentStatus != "CREATED") {
                        include "../mail/paymentUnsuccesfullSendEmail.php";
                        if (sendMail_paymentUnsuccesfullSendEmail($email, $orderNumber) == "Success") {
                            response("Request succesfull", "UNPAID - Email sent");
                            $logData .= " UNPAID - Email sent";  
                        }
                        else {response("Request succesfull", "UNPAID - Email not sent"); 
                            $logData .= " UNPAID - Email not sent";
                        } 
                    }                    
                } else {response("Request failed", "Php/db error" . $status_old); $logData .= " Php/db error";}
            } else {response("Requset failed", "Nothing to do?" . $status_old); $logData .= " Nothing to do?";}
        } else {response("Request failed", "GoPay error"); $logData .= " GoPay error"; }
    } else {response("Request failed", "gopay_id not found"); $logData .= " gopay_id not found"; }
} else {response("Request failed", "Invalid request"); $logData .= " Invalid request";  }
$logData .= " errors:".json_encode(error_get_last());

$ip = "unknown";
if (!empty($_SERVER['HTTP_CLIENT_IP']))  $ip = $_SERVER['HTTP_CLIENT_IP'];
elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
else $ip = $_SERVER['REMOTE_ADDR'];

//echo $logData;

$logPath = __DIR__ . "/cart.log";
$mode = (!file_exists($logPath)) ? 'w':'a';
$logfile = fopen($logPath, $mode);
fwrite($logfile, "\r\n". date("Y-m-d H:i:s") . " ip:".$ip . " paymentStatus.php - " . $logData);
fclose($logfile);






function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
