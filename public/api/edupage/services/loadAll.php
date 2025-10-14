<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function loadAll($month, $year) {
    global $conn;

    $data = [];

    $timestamp_from = "01.".$month.".".$year;
    if ($month == 12) $timestamp_to = "01.01." . $year+1;
    else $timestamp_to = "01.".$month+1 .".".$year;

    $timestamp_from = strtotime($timestamp_from);
    $timestamp_to = strtotime($timestamp_to);

    $sql = "SELECT * FROM edupage WHERE timestamp_created>$timestamp_from and timestamp_created<$timestamp_to";
    $response = mysqli_query($conn, $sql);
    if (mysqli_num_rows($response) > 0) {
        while($row = mysqli_fetch_assoc($response)) $data[] = $row;
    }
    return $data;
}
