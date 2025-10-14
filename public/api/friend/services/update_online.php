<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function update_online($user_id) {
    global $conn;
    $time = time();
    $sql = "UPDATE `user` SET online='$time' WHERE id='$user_id'"; 
    if (mysqli_query($conn, $sql)) return true;
    return false;
}
