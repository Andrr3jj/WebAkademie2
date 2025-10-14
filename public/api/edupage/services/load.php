<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function load($id) {
    global $conn;

    $data = "";

    $sql = "SELECT * FROM edupage WHERE id=$id";
    $response = mysqli_query($conn, $sql);
    if (mysqli_num_rows($response) > 0) {
        while($row = mysqli_fetch_assoc($response)) $data = $row;
    }
    return $data;
}
