<?php

include_once dirname(__FILE__) . "/../../dbh.api.php";

function folder_delete($name) {
    global $conn;
    $sql = "DELETE FROM `folder` WHERE name='$name'";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}
