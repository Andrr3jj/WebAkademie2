<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/is_admin.php";
include "./services/is_role.php";

include "./services/loadAll.php";


if (!is_admin() or !is_role()) response("Request failed", "Not logged in/Not permited");
$month = date("m");
if (!empty($_GET['month'])) $month = $_GET['month'];
$year = date("Y");
if (!empty($_GET['year'])) $year = $_GET['year'];
response("Request succesfull", loadAll($month, $year));
