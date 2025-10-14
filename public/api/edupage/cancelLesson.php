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
if (!$id) resposne("Request failed", "No id provided");
response("Request succesfull", cancelLesson($id));
