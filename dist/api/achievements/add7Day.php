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

if (!isset($_SESSION['add7Day_counter'])) $_SESSION['add7Day_counter'] = 0;
if (!isset($_SESSION['add7Day_counter_timeout'])) $_SESSION['add7Day_counter_timeout'] = time();

if ($_SESSION['add7Day_counter'] >= 10) response("Request succesfull", "Completed");

if (time() - $_SESSION['add7Day_counter_timeout'] < 50) response("Request failed", "timeout " . time() - $_SESSION['add7Day_counter_timeout']);

else {
    $_SESSION['add7Day_counter_timeout'] = time();
    $_SESSION['add7Day_counter'] += 1;
}

if ($_SESSION['add7Day_counter'] < 10) response("Request failed", "count " . $_SESSION['add7Day_counter']);

if (add_day($user_id)) response("Request succesfull");
else response("Request failed");
