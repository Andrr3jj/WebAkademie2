<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");

session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";



if (!is_logged_in()) response("Request failed", "Not logged in");
if (!isset($_GET['reward_id'])) response("Request failed", "Reward id not provided");
$reward_id = $_GET['reward_id'];
if (empty($_SESSION['id'])) response("Request failed", "Wrong login");
$user_id = $_SESSION['id'];

include "./services/claim_reward.php";
if (claim_reward($user_id, $reward_id)) response("Request succesfull");
response("Request failed");
