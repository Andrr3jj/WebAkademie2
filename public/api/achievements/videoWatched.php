<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");

session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/video_watched.php";


if (!is_logged_in()) response("Request failed", "Not logged in");
if (empty($_POST['id'])) response("Request failed", "No id provided");
$video_id = $_POST['id'];
if (empty($_SESSION['id'])) response("Request failed", "Wrong login");
$user_id = $_SESSION['id'];
$data = [];

if (video_watched($user_id, $video_id)) response("Request succesfull");
response("Request failed");
