<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");

session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/folder_exists.php";
include "./services/folder_list.php";





//if (empty($_GET['name'])) response("Request failed", "Folder name not provided");
$name = "";
if (!empty($_GET['name'])) $name = $_GET['name'];

if (!folder_exists($name) and $name) response("Request failed", "Folder does not exist");
response("Request succesfull", folder_list($name));
