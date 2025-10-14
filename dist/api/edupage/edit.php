<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/is_admin.php";
include "./services/is_role.php";

include "./services/edit.php";


if (!is_admin() or !is_role()) response("Request failed", "Not logged in/Not permited");
if (empty($_POST['id'])) response("Request failed", "No id provided");
$mode = "all";
if (!empty($_POST['mode'])) $mode = $_POST['mode'];

if ($mode == "all") if(edit_all()) response("Request succesfull");
if ($mode == "status") if(edit_status()) response("Request succesfull");
if ($mode == "teacher") if(edit_teacher()) response("Request succesfull");
response("Request failed");
