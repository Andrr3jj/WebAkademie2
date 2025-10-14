<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";
include "./services/is_logged_in.php";
include "./services/is_role.php";


include "./services/payment.php";

if (!is_logged_in() OR !is_role("edupageRegistrationsManager")) response("Request failed", "Not logged in");
response("Request succesfull", editPayment($_POST));
