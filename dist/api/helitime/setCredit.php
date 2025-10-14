<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");

session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/is_admin.php";
include "./services/set_credit.php";

if (!is_logged_in()) response("Request failed", "Not logged in");
if (!is_admin()) response("Request failed", "Not admin");
if (empty($_POST['id']) or empty($_POST['credit'])) response("Request failed", "(user)id or (new)credit not provided");
$user_id = $_POST['id'];
$credit = $_POST['credit'];
if (set_credit($user_id, $credit)) response("Request succesfull");
response("Request failed", "Error");
