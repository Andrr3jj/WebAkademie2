<?php


require_once str_replace("subscription", "cart", __DIR__) . '/GoPay/vendor/autoload.php';

use GoPay\Definition\Language;




if (!empty($_GET['id'])) {

    $gopay_id = $_GET['id'];

    
    
    
    include("../dbh.api.php");
    
    
    $sql = "SELECT * FROM `subscription` WHERE gopay_id='$gopay_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            $user_id = $row['user_id'];
            $status_old = $row['gopay_status'];
            $duration = $row['name'];

            $invoice_no = $row['invoice_no'];

            $inputData = json_decode($row['invoice_data'], true);
            
            
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


        include "../cart/GoPay.conf.php";
        $gopay = GoPay\payments([
            'goid' => $GoPay_config['goid'],
            'clientId' => $GoPay_config['clientId'],
            'clientSecret' => $GoPay_config['clientSecret'],
            'gatewayUrl' => $GoPay_config['gatewayUrl'],
            'language' => Language::SLOVAK
        ]);

        $response = $gopay->getStatus($gopay_id);

        if ($response->hasSucceed()) {
            $responseBody = $response->json;
            $paymentStatus = $responseBody['state'];

            if ($status_old == "CREATED") {
                $sql = "UPDATE `subscription` SET gopay_status='$paymentStatus' WHERE gopay_id='$gopay_id'";
                if (mysqli_query($conn, $sql)) {
                    if ($paymentStatus == "PAID") {
                        
                        include "../invoice/generateInvoice.php";
                        
                        $filepath = "../invoice/invoice_no.txt";
                        $invoice_no = file_get_contents($filepath) + 1;
                        $myfile = fopen($filepath, "w+");
                        fwrite($myfile, $invoice_no);
                        fclose($myfile);
        
                        //$invoice_no = "S" . strval(date("Y")) . strval($invoice_no);
                        $invoice_no = "Faktura-" . date("Y") . "-" . $invoice_no . "-S";
        
                        $invoice_path = "https://heligonkovaakademia.sk/data/uploads/invoices/" . $invoice_no;
        
                        $inputData['invoice_no'] = $invoice_no;

                        generateInvoice($inputData);

                        $sql_1 = "UPDATE `subscription` SET invoice_no='$invoice_no' WHERE gopay_id='$gopay_id'";
                        mysqli_query($conn, $sql_1);
                        $sql_2 = "UPDATE `subscription` SET invoice_path='$invoice_path' WHERE gopay_id='$gopay_id'";
                        mysqli_query($conn, $sql_2);


                        include "../mail/subscriptionPaymentSuccesfullSendEmail.php";
                        if (sendMail_subscriptionPaymentSuccesfullSendEmail(
                            $email, $duration, $invoice_no) == "Success"){
                                response("Request succesfull", "PAID - Email sent");   
                        //if (sendMail_subscriptionPaymentSuccesfullSendEmail(
                        //    $email, $invoice_no, $dateOfOrder, $name, $surname, 
                        //    $address, $order, $orderTotal, $address) == "Success"){
                        //        response("Request succesfull", "PAID - Email sent");   
                            } else response("Request succesfull", "PAID - Email not sent");  
                        
                    } else if ($paymentStatus != "CREATED") {
                        if ($paymentStatus != "TIMEOUTED") {
                            include "../mail/paymentUnsuccesfullSendEmail.php";
                            if (sendMail_paymentUnsuccesfullSendEmail($email, $invoice_no) == "Success") response("Request succesfull", "UNPAID - Email sent");   
                            else response("Request succesfull", "UNPAID - Email not sent");   
                        }
                        
                    } else {
                        response("Request succesfull", "CREATED - waiting for payment");
                    }


                    /*
                    if ($paymentStatus == "PAID") {
                    } else if ($paymentStatus == "TIMEOUTED" || $paymentStatus == "PAYMENT_METHOD_CHOSEN") {
                        response("Request succesfull", $paymentStatus);
                    } else if ($paymentStatus == "CANCELED") {
                        response("Request succesfull", $paymentStatus);
                    } else {
                        response("Request succesfull", $paymentStatus);
                    }
                    */
                    
                } else response("Request failed", "Php/db error" . $status_old);
            } else response("Requset failed", "Nothing to do?" . $status_old);
        } else response("Request failed", "GoPay error");
    } else response("Request failed", "gopay_id not found");
} else response("Request failed", "Invalid request");



function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
