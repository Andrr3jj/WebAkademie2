<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";
include "./services/is_logged_in.php";

include "./services/payment.php";

if (!is_logged_in()) response("Request failed", "Not logged in");
$id = $_SESSION['id'];
if (!empty($_GET['id'])) $id = $_GET['id'];
$month = date("m");
if (!empty($_GET['month'])) $month = $_GET['month'];
$year = date("Y");
if (!empty($_GET['year'])) $year = $_GET['year'];
response("Request succesfull", inquirePayment($id, $month, $year));
