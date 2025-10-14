<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");

session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/get_time.php";




//if (!is_logged_in()) response("Request failed", "Not logged in");
$user_id = 0;
if (!empty($_GET['id'])) $user_id = $_GET['id'];
response("Request succesfull", get_time($user_id));
