<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";
include "./services/is_logged_in.php";

include "./services/loadCalendar.php";


if (!is_logged_in()) response("Request failed", "Not logged in");
$id = 0;
if (!empty($_GET['id'])) $id = $_GET['id'];
$teacher_id = 0;
if (!empty($_GET['teacher_id'])) $teacher_id = $_GET['teacher_id'];
$student_id = 0;
if (!empty($_GET['student_id'])) $student_id = $_GET['student_id'];
response("Request succesfull", loadCalendar($id, $teacher_id, $student_id));
