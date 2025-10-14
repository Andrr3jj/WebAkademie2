<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";
include "./services/is_logged_in.php";

include "./services/manageCalendarCustom.php";


if (!is_logged_in()) response("Request failed", "Not logged in");
$teacher_id = 0;
if (!empty($_GET['teacher_id'])) $teacher_id = $_GET['teacher_id'];

response("Request succesfull", loadCalendarCustom($teacher_id));
