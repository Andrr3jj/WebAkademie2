<?php

include_once dirname(__FILE__) . "/../../dbh.api.php";

include_once dirname(__FILE__) . "/folder_list.php";

function folder_add($name, $id) {
    global $conn;

    $content = folder_list($name);
    if (in_array($id, $content)) return false;
    $content[] = $id;
    $content = json_encode($content);

    $sql = "UPDATE `folder` SET content='$content' WHERE name='$name'";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}
