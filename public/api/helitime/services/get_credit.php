<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function get_credit($user_id) {
    global $conn;
    $data = [];
    if ($user_id == -1 and !empty($_SESSION['id'])) $user_id = $_SESSION['id'];
    $sql = "SELECT id, credit FROM `user`";
    if ($user_id != 0) $sql .= " WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $data[$row['id']] = $row['credit'];
        }
    }
    return $data;
}
