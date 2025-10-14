<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function convert_time_to_credit($user_id) {
    global $conn;

    if ($user_id == -1 and !empty($_SESSION['id'])) $user_id = $_SESSION['id'];
    $sql = "SELECT helitime, helitime_credited, credit FROM `user` WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $helitime_old = $row['helitime'];
            $helitime_credited_old = $row['helitime_credited'];
            $credit_old = $row['credit'];
        }
    }
    
    $credit_new = $credit_old;
    $credit_new += $helitime_old - $helitime_credited_old;

    $helitime_credited_new = $helitime_old;

    $sql = "UPDATE `user` SET helitime_credited='$helitime_credited_new', credit='$credit_new' WHERE id='$user_id'";
    if (mysqli_query($conn, $sql)) return true;

    return false;
}
