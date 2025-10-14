<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/update_online.php";

if (!is_logged_in()) response("Request failed", "Not logged in");
$user_id = $_SESSION['id'];

if (update_online($user_id)) response("Request succesfull");
response("Request failed");
