<?php
include_once dirname(__FILE__) . "/../../dbh.api.php";

function folder_list($name = "") {
    global $conn;
    if (empty($name)) {
        $folders = [];
        $sql = "SELECT * FROM `folder`";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                if (!empty($row['name'])) $folders[] = $row['name'];
            }
        }
        return $folders;
    } 

    $content = [];
    $sql = "SELECT * FROM `folder` WHERE name='$name'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['content'])) $content = json_decode($row['content']);
        }
    }
    return $content;
}
