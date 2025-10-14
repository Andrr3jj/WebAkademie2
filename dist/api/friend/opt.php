<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/friend.php";

if (!is_logged_in()) response("Request failed", "Not logged in");
if (empty($_GET['accept'])) response("Request failed", "No decision provided");
if (empty($_GET['request_id'])) response("Request failed", "No id provided");
$user_id = $_SESSION['id'];
$accept = $_GET['accept'];
$request_id = $_GET['request_id'];


if ($accept == "yes") if (accept_request($request_id)) response("Request succesfull", "Accepted");
if (delete_request($request_id)) response("Request succesfull", "Deleted");
response("Request failed");
