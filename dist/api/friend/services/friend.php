<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function get_friendship_status($user_id, $friend_id) {
    global $conn;

    $sql = "SELECT * FROM friendship WHERE (user_id1='$user_id' and user_id2='$friend_id') 
                                        OR (user_id1='$friend_id' and user_id2='$user_id')
                                        ORDER BY id DESC";

    $r = mysqli_query($conn, $sql);
    if (mysqli_num_rows($r) > 0) {
        while($row = mysqli_fetch_assoc($r)) {
            return $row['status'];
        }
    }
    return "";
}



function send_request($user_id, $friend_id) {
    global $conn;

    if ($user_id == $friend_id) return false;
    if (get_friendship_status($user_id, $friend_id) != "") return false;

    $sql = "INSERT INTO friendship (user_id1, user_id2, status) VALUES ('$user_id','$friend_id','REQUEST')";

    if (mysqli_query($conn, $sql)) return true;
    return false;
}



function accept_request($request_id) {
    global $conn;

    $sql = "UPDATE friendship SET status='FRIENDSHIP' WHERE id='$request_id'";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}



function delete_request($request_id) {
    global $conn;
    
    $sql = "DELETE FROM friendship WHERE id='$request_id'";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}



function delete_friendship($friendship_id) {
    return delete_request($friendship_id);
}



function list_request($user_id) {
    global $conn;

    $data = [];
    $sql = "SELECT * FROM friendship WHERE status='REQUEST' AND user_id2='$user_id'";

    $r = mysqli_query($conn, $sql);
    if (mysqli_num_rows($r) > 0) {
        while($row = mysqli_fetch_assoc($r)) {
            $id = $row['user_id1'];
            if ($id == $user_id) $id = $row['user_id2'];
            $row['request_from'] = get_user_data($id);
            $data[] = $row;
        }
    }
    return $data;
}



function list_friendship($user_id) {
    global $conn;

    $data = [];
    $sql = "SELECT * FROM friendship WHERE status='FRIENDSHIP' AND (user_id1='$user_id' OR user_id2='$user_id')";

    $r = mysqli_query($conn, $sql);
    if (mysqli_num_rows($r) > 0) {
        while($row = mysqli_fetch_assoc($r)) {
            $id = $row['user_id1'];
            if ($id == $user_id) $id = $row['user_id2'];
            $data[$row['id']] = get_user_data($id);
        }
    }
    return $data;
}



function get_user_data($id) {
    global $conn;

    $sql = "SELECT id, name, surname, online, profilePhotoUrl, helitime FROM user WHERE id='$id'";

    $r = mysqli_query($conn, $sql);
    if (mysqli_num_rows($r) > 0) {
        while($row = mysqli_fetch_assoc($r)) {
            $row['achievements_finished_count'] = get_user_achivements_count($id);
            return $row;
        }
    }
}



function get_user_achivements_count ($user_id) {
    global $conn;

    include_once dirname(__DIR__) . "/../achievements/services/progress_load.php";
    $progress = progress_load($user_id);

    $finished = 0;
    foreach ($progress as $p) {
        if ($p['finished_date'] != "") $finished += 1; 
    }
    return $finished;
}
