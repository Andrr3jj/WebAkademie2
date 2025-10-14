<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");

session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/folder_exists.php";
include "./services/folder_rename.php";





if (empty($_POST['name'])) response("Request failed", "New folder name not provided");
$name = $_POST['name'];
if (empty($_POST['id'])) response("Request failed", "Folder id not provided");
$id = $_POST['id'];

if (!folder_exists($id)) response("Request failed", "Folder does not exist");
if (folder_rename($name, $id)) response("Request succesfull");
response("Request failed");
