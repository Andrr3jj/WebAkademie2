<?php
// /edupage/calendarSendEmailStudent.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
                  

require 'PHPMailer/vendor/autoload.php';


function sendMail_edupagePaymentNoticeSendEmail($email, $data) {
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


        //Content
        $mailBody = file_get_contents(__DIR__ . "/edupagePaymentNotice.html");
        $mailBody = str_replace("%name%", $data['name'], $mailBody);
        $mailBody = str_replace("%surname%", $data['surname'], $mailBody);
        $mailBody = str_replace("%price%", $data['price'], $mailBody);
        $mailBody = str_replace("%month%", $data['month'], $mailBody);
        $mailBody = str_replace("%year%", $data['year'], $mailBody);
        $mailBody = str_replace("%pay_before%", $data['pay_before'], $mailBody);
        $mailBody = str_replace("%qr%", $data['qr'], $mailBody);
        $mailBody = str_replace("%payment_note%", $data['payment_note'], $mailBody);
        $mailBody = str_replace("%lesson_count%", $data['lesson_count'], $mailBody);

        //$mailBody = str_replace("%data%", $data['data'], $mailBody);
        /*
        */

        //echo $mailBody;


        $altBody = file_get_contents(__DIR__ . "/edupagePaymentNotice.txt");
        //$altBody = str_replace("%data%", $data['data'], $altBody);


        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $mail_config['subject_edupagePaymentNotice'];
        $mail->Body    = $mailBody;
        $mail->AltBody = $altBody;

        $mail->send();
        return "Success";
    } catch (Exception $e) {
        //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        
        $logPath = __DIR__ . "/mail.log";
        $mode = (!file_exists($logPath)) ? 'w':'a';
        $logfile = fopen($logPath, $mode);
        fwrite($logfile, "\r\n". time() . "edupagePaymentNotice.php - ErrorMailerError:{$mail->ErrorInfo} (e={$e})");
        fclose($logfile);
        
        return "ErrorMailerError:{$mail->ErrorInfo}";
    }
}
 