<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/is_admin.php";
include "./services/is_role.php";

include "./services/addToOwnedAtLesson.php";


if (!is_admin()) response("Request failed", "Not logged in");
if (empty($_GET['user_id']) or empty($_GET['product_id']) or empty($_GET['lesson_id'])) response("Request failed");
$user_id = $_GET['user_id'];
$product_id = $_GET['product_id'];
$lesson_id = $_GET['lesson_id'];
if (addToOwnedAtLesson($user_id, $product_id, $lesson_id)) response("Request succesfull");
else response("Request failed");
