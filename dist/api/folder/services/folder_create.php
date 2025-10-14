<?php

include_once dirname(__FILE__) . "/../../dbh.api.php";

function folder_create($name, $type="product") {
    global $conn;
    $sql = "INSERT INTO `folder` (name, type) VALUES ('$name', '$type')";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}
