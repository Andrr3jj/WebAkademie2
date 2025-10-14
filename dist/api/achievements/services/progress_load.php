<?php

include_once dirname(__FILE__) . "/../../dbh.api.php";

function progress_load($user_id) {
    global $conn;

    $data = [];
    $sql = "SELECT achievements_data FROM `user` WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['achievements_data'])) $data = json_decode($row['achievements_data'], true);
        }
    }
    return $data;
}
