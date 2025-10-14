<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";
include "./services/is_logged_in.php";

include "./services/manageCalendarCustom.php";

if (!is_logged_in()) response("Request failed", "Not logged in");


//if (createCalendarCustom()) response("Request succesfull");
if (empty($_POST['id'])) {
    if (createCalendarCustom()) response("Request succesfull", "create");
    response("Request failed", "create");
};
if (editCalendarCustom()) response("Request succesfull", "edit");
response("Request failed", "edit");
