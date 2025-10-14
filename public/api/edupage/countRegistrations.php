<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/is_admin.php";
include "./services/is_role.php";

include "./services/countRegistrations.php";


if (!is_admin() or !is_role()) response("Request failed", "Not logged in/Not permited");
response("Request succesfull", countRegistrations());
