<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";
include "./services/is_logged_in.php";

include "./services/createCalendar.php";

if (!is_logged_in()) response("Request failed", "Not logged in");
if (empty($_POST['formOfStudy'])
    or !isset($_POST['availability_day'])
    or !isset($_POST['availability_hour'])) response("Request failed", "Not all required data provided");

if (createCalendar()) response("Request succesfull");
response("Request failed");
