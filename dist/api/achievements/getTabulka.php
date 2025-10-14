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


$data = [];

$sql_user = "SELECT id, helitime, name, surname, profilePhotoUrl FROM `user` WHERE is_admin='0' ORDER BY helitime DESC LIMIT 50";
$result_user = mysqli_query($conn, $sql_user);
if (mysqli_num_rows($result_user) > 0) {
    $count = 1;
    while($row = mysqli_fetch_assoc($result_user)) {
        $user_id = $row['id'];

        // pocet splnenych achievementov
        $progress = progress_load($user_id);
        $finished = 0;
        foreach ($progress as $p) {
            if ($p['finished_date'] != "") $finished += 1; 
        }

        $user['rank'] = $count;
        $user['name'] = $row['name'];
        $user['surname'] = $row['surname'];
        $user['profilePhotoUrl'] = $row['profilePhotoUrl'];
        $user['helitime'] = $row['helitime'];
        $user['finishedAchievements'] = $finished;


        $data[$count] = $user;

        $count += 1; 
    }
}






response("Request succesfull", $data);
