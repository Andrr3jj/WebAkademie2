<?php



function get_videos_watched_num() {
    global $conn;
    global $user_id;

    $videos_watched = 0;
    $sql = "SELECT videosWatched FROM `user` WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['videosWatched'])) $videos_watched = json_decode($row['videosWatched']);
            if ($videos_watched) $videos_watched = count($videos_watched);
        }
    }
    return $videos_watched;
}

function vyzvy_suvisiace_s_naucnimy_videami() {
    /* 
    * achievementy 200 - 299
    */

    global $conn;
    global $user_id;
    global $config;
    global $progress;


    $videos_watched = get_videos_watched_num();


    for ($achievement_id = 200; $achievement_id <= 299; $achievement_id++) {
        // check if achievement exists
        if (!array_key_exists($achievement_id, $config)) continue;


        $points_max = $config[$achievement_id]['points_max'];

        $finished_date = "";
        $reward_claimed = "false";
        if (array_key_exists($achievement_id, $progress)) {
            $finished_date = $progress[$achievement_id]['finished_date'];
            $reward_claimed = $progress[$achievement_id]['reward_claimed'];
        
        }

        $points_actual = $videos_watched;
        $videos_watched_max = $points_max;
        if ($videos_watched >= $videos_watched_max) {
            $points_actual = $videos_watched_max;
            if ($finished_date == "") $finished_date = date("d.m.Y");
        }
        $progress[$achievement_id] = [
            "achievement_id" => $achievement_id,
            "points_actual" => $points_actual,
            "finished_date" => $finished_date,
            "reward_claimed" => $reward_claimed
        ];
    } 
}
