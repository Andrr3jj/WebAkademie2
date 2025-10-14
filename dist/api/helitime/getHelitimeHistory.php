<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");

session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";

include "./services/get_helitime_history.php";

if (!is_logged_in()) response("Request failed", "Not logged in");
if (!empty($_GET['id'])) $user_id = $_GET['id'];
else $user_id = $_SESSION['id'];
response("Request succesfull", get_helitime_history($user_id));
