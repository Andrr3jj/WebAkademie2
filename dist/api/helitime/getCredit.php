<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");

session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/convert_time_to_credit.php";
include "./services/get_credit.php";

if (!is_logged_in()) response("Request failed", "Not logged in");
$user_id = -1;
if (!empty($_GET['id'])) $user_id = $_GET['id'];
convert_time_to_credit($user_id);
response("Request succesfull", get_credit($user_id));
