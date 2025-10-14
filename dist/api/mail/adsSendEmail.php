<?php
// skript na odosielanie overovacích emailov pri registrácií

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
require 'PHPMailer/vendor/autoload.php';

//Create an instance; passing `true` enables exceptions

function make_log() {
    if (empty($_SESSION['make_log_last']) or $_SESSION['make_log_last'] < time()) {
        include(__DIR__ . "/mail.conf.php");
        $subject = $mail_config['subject_ads'];
        if (!empty($_POST['subject'])) $subject = $_POST['subject'];
        include('../dbh.api.php');
        $sql = "INSERT INTO email_history (subject)
        VALUES ('$subject')";
        mysqli_query($conn, $sql);
        $_SESSION['make_log_last'] = time() + 10;
    }
}

function sendMail_adsSendEmail($email, $name, $surname, $image="") {
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
        //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mailBody = file_get_contents(__DIR__ . "/adsSendEmail.html");
        $mailBody = str_replace("%name%", $name, $mailBody);
        $mailBody = str_replace("%surname%", $surname, $mailBody);
        $mailBody = str_replace("%image%", $image, $mailBody);
        
        $altBody = file_get_contents(__DIR__ . "/adsSendEmail.txt");
        $altBody = str_replace("%name%", $name, $altBody);
        $altBody = str_replace("%surname%", $surname, $altBody);
        $altBody = str_replace("%image%", $image, $altBody);

        //$mail->addAttachment(/* cesta k súboru */);

        $subject = $mail_config['subject_ads'];
        if (!empty($_POST['subject'])) $subject = $_POST['subject'];


        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $mailBody;
        $mail->AltBody = $altBody;

        $mail->send();
        return "Success";
    } catch (Exception $e) {
        //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        
        $logPath = "mail.log";
        $mode = (!file_exists($logPath)) ? 'w':'a';
        $logfile = fopen($logPath, $mode);
        fwrite($logfile, "\r\n". time() . "adsSendEmail.php - ErrorMailerError:{$mail->ErrorInfo}");
        fclose($logfile);

        return "ErrorMailerError:{$mail->ErrorInfo}";
    }
}





header("Content-Type:application/json");

if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true and in_array("mail_master", $_SESSION['roles'])) {
    include('../dbh.api.php');

    $output_file = "";
    if (!empty($_POST['image'])) {
        //$base64_string = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAApgAAAKYB3X3/OAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAANCSURBVEiJtZZPbBtFFMZ/M7ubXdtdb1xSFyeilBapySVU8h8OoFaooFSqiihIVIpQBKci6KEg9Q6H9kovIHoCIVQJJCKE1ENFjnAgcaSGC6rEnxBwA04Tx43t2FnvDAfjkNibxgHxnWb2e/u992bee7tCa00YFsffekFY+nUzFtjW0LrvjRXrCDIAaPLlW0nHL0SsZtVoaF98mLrx3pdhOqLtYPHChahZcYYO7KvPFxvRl5XPp1sN3adWiD1ZAqD6XYK1b/dvE5IWryTt2udLFedwc1+9kLp+vbbpoDh+6TklxBeAi9TL0taeWpdmZzQDry0AcO+jQ12RyohqqoYoo8RDwJrU+qXkjWtfi8Xxt58BdQuwQs9qC/afLwCw8tnQbqYAPsgxE1S6F3EAIXux2oQFKm0ihMsOF71dHYx+f3NND68ghCu1YIoePPQN1pGRABkJ6Bus96CutRZMydTl+TvuiRW1m3n0eDl0vRPcEysqdXn+jsQPsrHMquGeXEaY4Yk4wxWcY5V/9scqOMOVUFthatyTy8QyqwZ+kDURKoMWxNKr2EeqVKcTNOajqKoBgOE28U4tdQl5p5bwCw7BWquaZSzAPlwjlithJtp3pTImSqQRrb2Z8PHGigD4RZuNX6JYj6wj7O4TFLbCO/Mn/m8R+h6rYSUb3ekokRY6f/YukArN979jcW+V/S8g0eT/N3VN3kTqWbQ428m9/8k0P/1aIhF36PccEl6EhOcAUCrXKZXXWS3XKd2vc/TRBG9O5ELC17MmWubD2nKhUKZa26Ba2+D3P+4/MNCFwg59oWVeYhkzgN/JDR8deKBoD7Y+ljEjGZ0sosXVTvbc6RHirr2reNy1OXd6pJsQ+gqjk8VWFYmHrwBzW/n+uMPFiRwHB2I7ih8ciHFxIkd/3Omk5tCDV1t+2nNu5sxxpDFNx+huNhVT3/zMDz8usXC3ddaHBj1GHj/As08fwTS7Kt1HBTmyN29vdwAw+/wbwLVOJ3uAD1wi/dUH7Qei66PfyuRj4Ik9is+hglfbkbfR3cnZm7chlUWLdwmprtCohX4HUtlOcQjLYCu+fzGJH2QRKvP3UNz8bWk1qMxjGTOMThZ3kvgLI5AzFfo379UAAAAASUVORK5CYII=";
        $base64_string = $_POST['image'];
        
        $output_file = time() . ".jpg"; 
        $ifp = fopen( $output_file, 'wb' ); 
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64_string );
        fwrite( $ifp, base64_decode( $data[ 1 ] ) );
        fclose( $ifp ); 
        
        $output_file = "https://heligonkovaakademia.sk/api/mail/" . $output_file;
    }
    
    $sql = "SELECT email, name, surname FROM user WHERE ads='true'";
    $result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) > 0) {
        make_log();
        $data = array();
  		while($row = mysqli_fetch_assoc($result)) {
            if (sendMail_adsSendEmail($row['email'], $row['name'], $row['surname'], $output_file) == "Success") array_push($data, "Succes:" . $row['email']);
            else array_push($data, "Error:" . $row['email']);
            sleep(60); // 60 emails/h
        }
        
        $logPath = "mail.log";
        $mode = (!file_exists($logPath)) ? 'w':'a';
        $logfile = fopen($logPath, $mode);
        fwrite($logfile, "\r\n". date("d.m.Y") . " adsSendEmail.php - " . json_encode($data));
        fclose($logfile);

        response("Request succesfull", $data);
    } else response("Request failed", "Email not found");

} else {
    response("Request failed", "Not logged in");
}



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
