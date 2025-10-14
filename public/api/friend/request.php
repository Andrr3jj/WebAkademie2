<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/friend.php";

if (!is_logged_in()) response("Request failed", "Not logged in");
if (empty($_GET['friend_id'])) response("Request failed", "No id provided");
$user_id = $_SESSION['id'];
$friend_id = $_GET['friend_id'];

if (send_request($user_id, $friend_id)) {
    response("Request successful");
} else {
    response("Request failed");
}