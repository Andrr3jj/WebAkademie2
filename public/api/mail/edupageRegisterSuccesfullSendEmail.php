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

function sendMail_edupageRegisterSuccesfullSendEmail($email, $post) {
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
        $mailBody = file_get_contents(__DIR__ . "/edupageReigsterSuccesfullSendEmail.html");
        $mailBody = str_replace("%registerer%", $post['registerer'], $mailBody);
        $mailBody = str_replace("%name%", $post['name'], $mailBody);
        $mailBody = str_replace("%surname%", $post['surname'], $mailBody);
        $mailBody = str_replace("%placeOfBirth%", $post['placeOfBirth'], $mailBody);
        $mailBody = str_replace("%address%", $post['address'], $mailBody);
        $mailBody = str_replace("%dateOfBirth%", $post['dateOfBirth'], $mailBody);
        $mailBody = str_replace("%experience%", $post['experience'], $mailBody);
        $mailBody = str_replace("%email%", $post['email'], $mailBody);
        $mailBody = str_replace("%phone%", $post['phone'], $mailBody);
        $mailBody = str_replace("%placeOfStudy%", $post['placeOfStudy'], $mailBody);
        $mailBody = str_replace("%formOfStudy%", $post['formOfStudy'], $mailBody);
        $mailBody = str_replace("%note%", $post['note'], $mailBody);
        $mailBody = str_replace("%approximateTimeOfStudy%", $post['approximateTimeOfStudy'], $mailBody);

        //echo $mailBody;


        $altBody = file_get_contents(__DIR__ . "/edupageReigsterSuccesfullSendEmail.txt");
        $altBody = str_replace("%registerer%", $post['registerer'], $altBody);
        $altBody = str_replace("%name%", $post['name'], $altBody);
        $altBody = str_replace("%surname%", $post['surname'], $altBody);
        $altBody = str_replace("%placeOfBirth%", $post['placeOfBirth'], $altBody);
        $altBody = str_replace("%address%", $post['address'], $altBody);
        $altBody = str_replace("%dateOfBirth%", $post['dateOfBirth'], $altBody);
        $altBody = str_replace("%experience%", $post['experience'], $altBody);
        $altBody = str_replace("%email%", $post['email'], $altBody);
        $altBody = str_replace("%phone%", $post['phone'], $altBody);
        $altBody = str_replace("%placeOfStudy%", $post['placeOfStudy'], $altBody);
        $altBody = str_replace("%formOfStudy%", $post['formOfStudy'], $altBody);
        $altBody = str_replace("%note%", $post['note'], $altBody);
        $altBody = str_replace("%approximateTimeOfStudy%", $post['approximateTimeOfStudy'], $altBody);

        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $mail_config['subject_edupageRegister'];
        $mail->Body    = $mailBody;
        $mail->AltBody = $altBody;

        $mail->send();
        return "Success";
    } catch (Exception $e) {
        //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        
        $logPath = __DIR__ . "/mail.log";
        $mode = (!file_exists($logPath)) ? 'w':'a';
        $logfile = fopen($logPath, $mode);
        fwrite($logfile, "\r\n". time() . "edupageReigsterSuccesfullSendEmail.php - ErrorMailerError:{$mail->ErrorInfo} (e={$e})");
        fclose($logfile);
        
        return "ErrorMailerError:{$mail->ErrorInfo}";
    }
}
 