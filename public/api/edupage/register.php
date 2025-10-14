<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";
include "./services/register.php";

if (empty($_POST['registerer']) 
    or empty($_POST['placeOfStudy'])
    or empty($_POST['formOfStudy'])
    or empty($_POST['approximateTimeOfStudy'])) response("Request failed", "Not all required data provided");

if (register()) {
    include_once "../mail/edupageRegisterSuccesfullSendEmail.php";
    if (!empty($_POST['email']) and sendMail_edupageRegisterSuccesfullSendEmail($_POST['email'], $_POST) == "Success") response("Request succesfull", "Email sent");
    response("Request succesfull", "Email not sent");
} 
response("Request failed");
