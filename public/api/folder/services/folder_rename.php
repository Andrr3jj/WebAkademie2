<?php

include_once dirname(__FILE__) . "/../../dbh.api.php";

include_once dirname(__FILE__) . "/folder_list.php";

function folder_rename($name, $id) {
    global $conn;

    $sql = "UPDATE `folder` SET name='$name' WHERE id='$id'";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}
