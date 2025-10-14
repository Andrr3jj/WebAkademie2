<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";
include "./services/is_logged_in.php";

include "./services/loadStudentAdvanced.php";

if (!is_logged_in()) response("Request failed", "Not logged in");
$id = $_SESSION['id'];
if (!empty($_GET['id'])) $id = $_GET['id'];
response("Request succesfull", loadStudentsAdvanced($id));
