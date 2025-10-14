<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/friend.php";

if (!is_logged_in()) response("Request failed", "Not logged in");
$user_id = $_SESSION['id'];

response("Request succesfull", list_friendship($user_id));
