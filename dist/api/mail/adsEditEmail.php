<?php
header("Content-Type:application/json");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
    
    $json_response = json_encode($response);
    echo $json_response;
}

if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true and in_array("mail_master", $_SESSION['roles'])) {
    $email_txt = fopen("adsSendEmail.txt", "w+");
    $email_html = fopen("adsSendEmail.html", "w+");

    if ($email_txt === false || $email_html === false) {
        response("Request failed", "Could not open files");
        exit();
    }

    if ( !empty($_POST['email_txt']) ) {
        fwrite($email_txt, $_POST['email_txt']);
    }
    if ( !empty($_POST['email_html']) ) {
        fwrite($email_html, $_POST['email_html']);
    }

    fseek($email_txt, 0); // Reset file pointer to the beginning
    fseek($email_html, 0); // Reset file pointer to the beginning

    $email_txt_content = "";
    $email_html_content = "";
    
    if(filesize("adsSendEmail.txt") > 0) $email_txt_content = fread($email_txt, filesize("adsSendEmail.txt"));
    if(filesize("adsSendEmail.html") > 0) $email_html_content = fread($email_html, filesize("adsSendEmail.html"));

    $data = [
        'email_txt' => $email_txt_content,
        'email_html' => $email_html_content
    ];

    fclose($email_txt);
    fclose($email_html);

    response("Request succesfull", $data);
} else {
    response("Request failed", "Not logged in");
}
?>
