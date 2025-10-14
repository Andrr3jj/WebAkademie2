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

function sendMail_changeStatusSendEmail($meno, $priezvisko, $email, $cislo_objednavky,$datum_objednavky,$datum_odoslania,$fakturacna_adresa,$dodacia_adresa,$sposob_dopravy,$objednany_tovar,$cena_celkom) {
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

        $objednany_tovar_new = '<div class="objednavka">';
        foreach ($objednany_tovar as $ots => $value) {// štruktúra vzhľadu produktov  
            $name = $value['name'];
            $qty = $value['qty'];
            $price = $value['price'];
            $photo = $value['photo'];
            if ($price < 0) $photo = "none";
            
            /* 
            ?? price < 0 mozno zlava
            ?? photo != none mozno realny produkt
            */
            $images_ext = ["jpg", "png", "jpeg"];
            $typ = "Číselné zápisy";
            if ($photo != "none") $typ = "Produkt";
            if (!in_array(substr($photo, -3), $images_ext)) $photo = "https://heligonkovaakademia.sk/data/images/CiselneZapisyTitle.png";
            $sd = $name . " (" . number_format((float)$price, 2, ',', '') . "€)";
            if ($sd == $sposob_dopravy) $typ = "Zľava"; // Zlava <> Doprava
            if ($price < 0) $typ = "Zľava";

            $price = number_format((float)$price, 2, ',', '');




            $otns = '
            <ul class="cart">
            <li class="cart-item">
            <img src="' . $photo . '" alt="obrazok" class="noty" />
            <div class="line"></div>
            <div class="specifikacia">
            <p class="nazov">
            ' . $typ . '
            </p>
            <p class="nazov-piesne">
            '.$name.'
            </p>
            <p class="nazov-piesne">
            Množstvo: '.$qty.'
            </p>
            <p class="cena">
            '.number_format((float)$price, 2, ',', '').' €
            </p>
            </div>
            </li>
            </ul>';

            if ($price < 0) {
                $zlava_kod = $name;
                $zlava_suma = number_format((float)$price, 2, ',', '');
            } else {
                if ($typ != "Zľava") $objednany_tovar_new .= $otns;
            }

        }

        if (!empty($zlava_kod)) {
            $otns = '
            <p class="nazov-piesne">
            Kód: '.$zlava_kod.'                  '.$zlava_suma.' €</p>';
            $objednany_tovar_new .= $otns;
        }
        
        //Content
        $mailBody = file_get_contents(__DIR__ . "/changeStatusSendEmail.html");
        $mailBody = str_replace("%meno%", $meno, $mailBody);
        $mailBody = str_replace("%priezvisko%", $priezvisko, $mailBody);
        $mailBody = str_replace("%cislo_objednavky%", $cislo_objednavky, $mailBody);
        $mailBody = str_replace("%datum_objednavky%", $datum_objednavky, $mailBody);
        $mailBody = str_replace("%datum_odoslania%", $datum_odoslania, $mailBody);
        $mailBody = str_replace("%fakturacna_adresa%", $fakturacna_adresa, $mailBody);
        $mailBody = str_replace("%dodacia_adresa%", $dodacia_adresa, $mailBody);
        $mailBody = str_replace("%sposob_dopravy%", $sposob_dopravy, $mailBody);
        $mailBody = str_replace("%objednany_tovar%", $objednany_tovar_new, $mailBody);
        $mailBody = str_replace("%cena_celkom%", $cena_celkom, $mailBody);

        $altBody = file_get_contents(__DIR__ . "/changeStatusSendEmail.txt");
        $altBody = str_replace("%meno%", $meno, $altBody);
        $altBody = str_replace("%priezvisko%", $priezvisko, $altBody);
        $altBody = str_replace("%cislo_objednavky%", $cislo_objednavky, $altBody);
        $altBody = str_replace("%datum_objednavky%", $datum_objednavky, $altBody);
        $altBody = str_replace("%datum_odoslania%", $datum_odoslania, $altBody);
        $altBody = str_replace("%fakturacna_adresa%", $fakturacna_adresa, $altBody);
        $altBody = str_replace("%dodacia_adresa%", $dodacia_adresa, $altBody);
        $altBody = str_replace("%sposob_dopravy%", $sposob_dopravy, $altBody);
        $altBody = str_replace("%objednany_tovar%", $objednany_tovar_new, $altBody);
        $altBody = str_replace("%cena_celkom%", $cena_celkom, $altBody);


        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $mail_config['subject_paymentSuccesfull'];
        $mail->Body    = $mailBody;
        $mail->AltBody = $altBody;

        //echo $mailBody;
        $mail->send();
        return "Success";
    } catch (Exception $e) {
        //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        
        $logPath = __DIR__ . "/mail.log";
        $mode = (!file_exists($logPath)) ? 'w':'a';
        $logfile = fopen($logPath, $mode);
        fwrite($logfile, "\r\n". time() . "changeStatusSendEmail.php - ErrorMailerError:{$mail->ErrorInfo} (invoicePath={$invoicePath}) (e={$e})");
        fclose($logfile);
        
        return "ErrorMailerError:{$mail->ErrorInfo}";
    }
}
