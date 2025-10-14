<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");

session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/is_admin.php";
include "./services/folder_exists.php";
include "./services/folder_create.php";


if (!is_logged_in()) response("Request failed", "Not logged in");
if (!is_admin()) response("Request failed", "Not admin");
if (empty($_SESSION['id'])) response("Request failed", "Wrong login");
if (empty($_POST['name'])) response("Request failed", "Folder name not provided");
$name = $_POST['name'];
$type = "product";
if (!empty($_POST['type'])) $type = $_POST['type'];
if (folder_exists($name)) response("Request failed", "Folder already exists");
if (folder_create($name, $type)) response("Request succesfull");
response("Request failed");
