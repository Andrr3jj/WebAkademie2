<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");

session_start();
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/is_admin.php";


if (!is_logged_in()) response("Request failed", "Not logged in");
if (!is_admin()) response("Request failed", "Not admin");

if (empty($_POST['settings'])) response("Request failed", "(new)settings not provided");
$settings = $_POST['settings'];
if (!json_decode($settings, true)) response("Request failed", "Invalid JSON");
if (file_put_contents("./config/helitime.config.json", $settings)) response("Request succesfull");
else response("Request failed");
