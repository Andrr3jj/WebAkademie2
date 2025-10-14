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

function sendMail_paymentSuccesfullSendEmailGiftCard($email, $user_name, $surname, $ngc) {
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




        $new_gift_card_value = $ngc["value"];
        $new_gift_card_valid_until = $ngc["date"];
        $new_gift_card_code = $ngc["code"];

        $text = "Platí do: " . $new_gift_card_valid_until . "  Kód: " . $new_gift_card_code; 

        //$image = new Imagick();
        //$image_val = new Imagick();
        //$draw = new ImagickDraw();
//
//
        //$image->readImage("./kupon_background.png");
        $image = "https://www.heligonkovaakademia.sk/api/mail/kupon_background.png";
        //$image_val->readImage("./".$new_gift_card_value.".png");
        //
        $coupon = "https://www.heligonkovaakademia.sk/api/mail/".$new_gift_card_value.".png";
        //$image_val->scaleImage(230, 0);
        //$image->compositeImage($image_val, imagick::COMPOSITE_OVER, 220, 525);
        //
        //$draw->setFillColor('black');
        //$draw->setFont('Bookman-DemiItalic');
        //$draw->setFontSize(13);
        //$image->annotateImage($draw, 180, 915, 0, $text);
        //
//
//
        //$mail->addStringEmbeddedImage($image, '1', "poukazka.png", 'base64', 'image/png');


        //Content
        $mailBody = file_get_contents(__DIR__ . "/paymentSuccesfullSendEmailGiftCard.html");
        $mailBody = str_replace("%image%", $image, $mailBody);
        $mailBody = str_replace("%coupon%", $coupon, $mailBody);
        $mailBody = str_replace("%text%", $text, $mailBody);

        $mailBody = str_replace("%name%", $user_name, $mailBody);
        $mailBody = str_replace("%surname%", $surname, $mailBody);
        //$mailBody = str_replace("%image%", "cid:1", $mailBody);
        echo $mailBody;


        $altBody = file_get_contents(__DIR__ . "/paymentSuccesfullSendEmailGiftCard.txt");
        $altBody = str_replace("%name%", $user_name, $altBody);
        $altBody = str_replace("%surname%", $surname, $altBody);
        $altBody = str_replace("%image%", "cid:1", $altBody);

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
        fwrite($logfile, "\r\n". time() . "paymentSuccesfullSendEmailCombined.php - ErrorMailerError:{$mail->ErrorInfo} (invoicePath={$invoicePath}) (e={$e})");
        fclose($logfile);
        
        return "ErrorMailerError:{$mail->ErrorInfo}";
    }
}
 