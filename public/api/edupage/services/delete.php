<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function delete($id) {
    global $conn;


    $sql = "DELETE FROM edupage WHERE id=$id";
    //$sql_future = "UPDATE edupage_lesson SET status='canceled' WHERE status='planned'";
    $sql_future = "DELETE FROM edupage_lesson WHERE status='planned'";
    if (mysqli_query($conn, $sql) and mysqli_query($conn, $sql_future)) return true;
    return false;
}
