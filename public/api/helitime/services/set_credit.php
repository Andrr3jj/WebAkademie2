<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function set_credit($user_id, $credit) {
    global $conn;
    $sql = "UPDATE `user` SET credit='$credit' WHERE id='$user_id'";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}
