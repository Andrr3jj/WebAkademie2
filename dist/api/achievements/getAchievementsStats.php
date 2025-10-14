<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");

session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/progress_load.php";


if (!is_logged_in()) response("Request failed", "Not logged in");
if (empty($_SESSION['id'])) response("Request failed", "Wrong login");
$user_id = $_SESSION['id'];
$config = json_decode(file_get_contents("./config/achievements.config.json"), true);

$progress = progress_load($user_id);

$finished = 0;
$total = -1; // - 7day
$total = count($config) - 1;

foreach ($progress as $p) {
    if ($p['finished_date'] != "") $finished += 1; 
}

$data['total'] = $total;
$data['finished'] = $finished;


response("Request succesfull", $data);
