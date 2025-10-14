<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";
include "./services/is_logged_in.php";

include "./services/lesson.php";

if (!is_logged_in()) response("Request failed", "Not logged in");

$id = !empty($_GET['id']) ? $_GET['id'] : 0;
$calendar_id = !empty($_GET['calendar_id']) ? $_GET['calendar_id'] : 0;
$date = isset($_GET['date']) ? $_GET['date'] : '';

response("Request succesfull", loadLesson($id, $calendar_id, $date));
