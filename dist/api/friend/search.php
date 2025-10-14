<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/search.php";

if (!is_logged_in()) response("Request failed", "Not logged in");
if (empty($_GET['search'])) response("Request failed", "No search param provided");
$search = $_GET['search'];

response("Request succesfull", search($search));
