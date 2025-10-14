<?php

include_once dirname(__FILE__) . "/../../dbh.api.php";

function video_watched($user_id, $video_id) {
    global $conn;

    $videosWatched = [];
    $sql = "SELECT videosWatched FROM user WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['videosWatched'])) $videosWatched = json_decode($row['videosWatched']);
        }
    }

    if (!in_array($video_id, $videosWatched)) $videosWatched[] = $video_id;
    $videosWatched = json_encode($videosWatched);

    $sql = "UPDATE user SET videosWatched='$videosWatched' WHERE id='$user_id'";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}
