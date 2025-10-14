<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");

session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/get_credit.php";

if (!is_logged_in()) response("Request failed", "Not logged in");

$credit = get_credit(-1); 
if (empty($credit)) $credit = 0;
else $credit = $credit[array_key_first($credit)];

$credit_trigger_value_notification = json_decode(file_get_contents("./config/helitime.config.json"), true)["credit_trigger_value_notification"];

if ($credit > $credit_trigger_value_notification) response("Request succesfull", "true");
response("Request succesfull", "false");
