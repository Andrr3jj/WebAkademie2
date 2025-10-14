<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function get_helitime_history($user_id) {
    global $conn;
    $sql = "SELECT helitime_history FROM `user` WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            return $row['helitime_history'];
        }
    }
    return "";
}
