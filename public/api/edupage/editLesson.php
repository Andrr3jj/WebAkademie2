<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";
include "./services/is_logged_in.php";

include "./services/lesson.php";

if (!is_logged_in()) response("Request failed", "Not logged in");
if (empty($_POST['id'])) response("Request failed", "Not all required parameters provided");
$id = $_POST['id'];
response("Request succesfull", editLesson($id));
