<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function delete($id) {
    global $conn;


    $sql = "DELETE FROM edupage WHERE id=$id";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}
