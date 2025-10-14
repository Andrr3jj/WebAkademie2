<?php
// /edupage/calendarSendEmailStudent.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
                  

require 'PHPMailer/vendor/autoload.php';


function sendMail_edupageCalendarSendEmail($email, $data) {
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
        $mailBody = file_get_contents(__DIR__ . "/edupageCalendarSendEmail.html");
        $mailBody = str_replace("%parentName%", $data['custom']['parentName'], $mailBody);
        $mailBody = str_replace("%studentName%", $data['info']['name'], $mailBody);
        $mailBody = str_replace("%placeOfStudy%", $data['info']['placeOfStudy'], $mailBody);
        $mailBody = str_replace("%dayOfStudy%", $data['calendar']['availability_day'], $mailBody);
        $mailBody = str_replace("%timeOfStudy%", $data['calendar']['availability_hour'], $mailBody);
        $mailBody = str_replace("%formOfStudy%", $data['info']['formOfStudy'], $mailBody);
        $mailBody = str_replace("%teacherName%", $data['custom']['teacherName'], $mailBody);
        $mailBody = str_replace("%teacherEmail%", $data['custom']['teacherEmail'], $mailBody);
        $mailBody = str_replace("%teacherPhone%", $data['custom']['teacherPhone'], $mailBody);
        //$mailBody = str_replace("%data%", $data['data'], $mailBody);
        /*
        *educale $data['calendar']
        *edupage $data['info']
        *edupage $data['custom']
        */

        //echo $mailBody;


        $altBody = file_get_contents(__DIR__ . "/edupageCalendarSendEmail.txt");
        //$altBody = str_replace("%data%", $data['data'], $altBody);


        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $mail_config['subject_edupageCalendarEmail'];
        $mail->Body    = $mailBody;
        $mail->AltBody = $altBody;

        $mail->send();
        return "Success";
    } catch (Exception $e) {
        //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        
        $logPath = __DIR__ . "/mail.log";
        $mode = (!file_exists($logPath)) ? 'w':'a';
        $logfile = fopen($logPath, $mode);
        fwrite($logfile, "\r\n". time() . "edupageCalendarSendEmail.php - ErrorMailerError:{$mail->ErrorInfo} (e={$e})");
        fclose($logfile);
        
        return "ErrorMailerError:{$mail->ErrorInfo}";
    }
}
 