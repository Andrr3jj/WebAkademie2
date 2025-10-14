<?php

include_once dirname(__FILE__) . "/../../dbh.api.php";

function progress_save($user_id, $achievements_data) {
    global $conn;

    $achievements_data = json_encode($achievements_data);
    $sql = "UPDATE `user` SET achievements_data='$achievements_data' WHERE id='$user_id'";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}
