<?php
include_once dirname(__FILE__) . "/../../dbh.api.php";

function folder_exists($identifier) {
    global $conn;
    $sql = "SELECT id FROM `folder` WHERE ";
    if (is_numeric($identifier)) $sql .= "id";
    else $sql .= "name";
    $sql .= "='$identifier'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) return true;
    return false;
}
