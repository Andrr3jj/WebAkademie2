<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");

session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/7_day.php";


if (!is_logged_in()) response("Request failed", "Not logged in");
if (empty($_SESSION['id'])) response("Request failed", "Wrong login");
$user_id = $_SESSION['id'];


if (claim_7day($user_id)) response("Request succesfull");
response("Request failed");
