<?php

function get_time_hours() {
    global $conn;
    global $user_id;

    $time_minutes = 0;
    $sql = "SELECT helitime FROM `user` WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            $time_minutes = $row['helitime'];
        }
    }
    $time_hours = $time_minutes / 60;
    return $time_hours;
}

function vyzvy_suvisiace_s_ucenim() {
    /* 
    * achievementy 0 - 99
    */

    global $conn;
    global $user_id;
    global $config;
    global $progress;


    $time_hours = get_time_hours();

    for ($achievement_id = 0; $achievement_id <= 99; $achievement_id++) {
        // check if achievement exists
        if (!array_key_exists($achievement_id, $config)) continue;


        $points_max = $config[$achievement_id]['points_max'];

        $finished_date = "";
        $reward_claimed = "false";
        if (array_key_exists($achievement_id, $progress)) {
            $finished_date = $progress[$achievement_id]['finished_date'];
            $reward_claimed = $progress[$achievement_id]['reward_claimed'];
        }

        $points_actual = $time_hours;
        $time_hours_max = $points_max;
        if ($time_hours >= $time_hours_max) {
            $points_actual = $time_hours_max;
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
