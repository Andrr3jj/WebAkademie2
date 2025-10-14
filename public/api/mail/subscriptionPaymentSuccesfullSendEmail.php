<?php
// skript na odosielanie overovacích emailov pri registrácií

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
                  
//Load Composer's autoloader
require 'PHPMailer/vendor/autoload.php';

//Create an instance; passing `true` enables exceptions

//function sendMail_subscriptionPaymentSuccesfullSendEmail($email, $orderNumber, $dateOfOrder, $name, $surname, $address, $order, $orderTotal, $billing_address) {
function sendMail_subscriptionPaymentSuccesfullSendEmail($email, $duration, $invoicePath = "") {
    $mail = new PHPMailer(true);
    $mail->CharSet = "UTF-8";
    try {
        $mail->setLanguage('sk', 'PHPMailer/vendor/phpmailer/phpmailer/language/');

        include(__DIR__ . "/mail.conf.php");
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = $mail_config['Host'];                     //Set the SMTP server to send through
        $mail->SMTPAuth   = $mail_config['SMTPAuth'];                                   //Enable SMTP authentication
        $mail->Username   = $mail_config['Username'];                     //SMTP username
        $mail->Password   = $mail_config['Password'];                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom($mail_config['setFrom'][0], $mail_config['setFrom'][1]); // email@email.com, email
        $mail->addReplyTo($mail_config['addReplyTo'][0], $mail_config['addReplyTo'][1]); // email@email.com, email
        
        $mail->addAddress($email);

        //Attachments
        if (!empty($invoicePath))$mail->addAttachment(realpath(__DIR__ . "/../../data/uploads/invoices/") . "/" . $invoicePath . ".pdf");      
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name


        
        //Content
        $mailBody = file_get_contents(__DIR__ . "/subscriptionPaymentSuccesfullSendEmail.html");
        $mailBody = str_replace("%duration%", $duration, $mailBody);
        //$mailBody = str_replace("%orderNumber%", $orderNumber, $mailBody);
        //$mailBody = str_replace("%dateOfOrder%", $dateOfOrder, $mailBody);
        //$mailBody = str_replace("%name%", $name, $mailBody);
        //$mailBody = str_replace("%surname%", $surname, $mailBody);
        //$mailBody = str_replace("%address%", $address, $mailBody);
        //$mailBody = str_replace("%order%", $order, $mailBody);
        //$mailBody = str_replace("%orderTotal%", $orderTotal, $mailBody);
        //$mailBody = str_replace("%billing_address%", $billing_address, $mailBody);

        $altBody = file_get_contents(__DIR__ . "/subscriptionPaymentSuccesfullSendEmail.txt");
        $altBody = str_replace("%duration%", $duration, $altBody);
        //$altBody = str_replace("%orderNumber%", $orderNumber, $altBody);
        //$altBody = str_replace("%dateOfOrder%", $dateOfOrder, $altBody);
        //$altBody = str_replace("%name%", $name, $altBody);
        //$altBody = str_replace("%surname%", $surname, $altBody);
        //$altBody = str_replace("%address%", $address, $altBody);
        //$altBody = str_replace("%order%", $order, $altBody);
        //$altBody = str_replace("%orderTotal%", $orderTotal, $altBody);
        //$altBody = str_replace("%billing_address%", $billing_address, $altBody);

        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $mail_config['subject_paymentSuccesfull'];
        $mail->Body    = $mailBody;
        $mail->AltBody = $altBody;

        $mail->send();
        return "Success";
    } catch (Exception $e) {
        //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        
        $logPath = __DIR__ . "/mail.log";
        $mode = (!file_exists($logPath)) ? 'w':'a';
        $logfile = fopen($logPath, $mode);
        fwrite($logfile, "\r\n". time() . "ErrorMailerError:{$mail->ErrorInfo}");
        fclose($logfile);
        
        return "ErrorMailerError:{$mail->ErrorInfo}";
    }
}
