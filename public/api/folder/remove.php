<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");

session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/folder_exists.php";
include "./services/folder_remove.php";





if (empty($_POST['name'])) response("Request failed", "Folder name not provided");
$name = $_POST['name'];
if (empty($_POST['id'])) response("Request failed", "Object id not provided");
$id = $_POST['id'];

if (!folder_exists($name)) response("Request failed", "Folder does not exist");
if (folder_remove($name, $id)) response("Request succesfull");
response("Request failed");
