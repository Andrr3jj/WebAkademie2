<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function countRegistrations() {
    global $conn;

    $data = [];
    $data['count_all'] = 0;
    $data['count_čaká sa'] = 0;
    $data['count_zaradený'] = 0;
    $data['count_odhlásený'] = 0;

    $sql = "SELECT * FROM edupage";
    $response = mysqli_query($conn, $sql);
    if (mysqli_num_rows($response) > 0) {
        $data['count_all'] = mysqli_num_rows($response);
        while($row = mysqli_fetch_assoc($response)) {
            $count_key = $row['status'];
            if (!isset($data["count_" . $count_key])) $data["count_" . $count_key] = 0;
            $data["count_" . $count_key] += 1;
        }
    }
    return $data;
}
