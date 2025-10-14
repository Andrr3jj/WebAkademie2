<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
                  
//Load Composer's autoloader
require '../mail/PHPMailer/vendor/autoload.php';

include("../antispam.php");


if ( !empty($_POST['name']) and !empty($_POST['email']) and !empty($_POST['message']) ) {
    $form_site = "pomoc";
    $form_data = array();
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $form_data["name"] = $name; $form_data["email"] = $email; $form_data["message"] = $message;
    $form_data = json_encode($form_data);
    $session = json_encode($_SESSION);
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    if ( !(strlen($name) > 100 or strlen($email) > 100 or strlen($message) > 10000) ) {
        include('../dbh.api.php');
        
        //mail('info@heligonkovaakademia.sk', 'Nová správa zo stránky heligonkovaakademia.sk', $message . "\n\n odoslané od: " . $name . " " . $email; charset=UTF-8");
        


        $mail = new PHPMailer(true);
        $mail->CharSet = "UTF-8";
        try {
            $mail->setLanguage('sk', 'PHPMailer/vendor/phpmailer/phpmailer/language/');

            include(__DIR__ . "/../mail/mail.conf.php");
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

            $mail->addAddress("info@heligonkovaakademia.sk");
            //$mail->addAddress("test@heligonkovaakademia.sk");

            //Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mailBody = $message . "\n\n odoslané od: " . $name . " " . $email;


            $altBody = $message . "\n\n odoslané od: " . $name . " " . $email;


            //$mail->addAttachment(/* cesta k súboru */);
            
            //$subject = $mail_config['subject_ads'];
            $subject = "Nová správa zo stránky heligonkovaakademia.sk";
            
            
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $mailBody;
            $mail->AltBody = $altBody;
            
            $mail->send();

        //return "Success";
        } catch (Exception $e) {
            //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";

            //$logPath = "mail.log";
            //$mode = (!file_exists($logPath)) ? 'w':'a';
            //$logfile = fopen($logPath, $mode);
            //fwrite($logfile, "\r\n". time() . "adsSendEmail.php - ErrorMailerError:{$mail->ErrorInfo}");
            //fclose($logfile);

            //return "ErrorMailerError:{$mail->ErrorInfo}";
        }
        
        $sql = "INSERT INTO form (form_site, form_data, session, ip) 
                VALUES ('$form_site', '$form_data', '$session', '$ip')";
        if (mysqli_query($conn, $sql)) response("Request succesfull");
        else response("Request failed");            
    } else response("Request failed", "Data too long");
} else response("Invalid request");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
