<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

include "./services/get_helitime_history.php";

function set_time($user_id, $time) {
    global $conn;
    //$helitime_history = get_helitime_history($user_id) . "\n|" . date("d.m.Y s:i:G") . " new_time:" . $time;
    $sql = "UPDATE `user` SET helitime='$time' WHERE id='$user_id'"; // , helitime_history='$helitime_history'
    if (mysqli_query($conn, $sql)) return true;
    return false;
}
