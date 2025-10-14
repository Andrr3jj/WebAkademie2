<?php

include_once dirname(__FILE__) . "/../../dbh.api.php";

include_once dirname(__FILE__) . "/progress_load.php";
include_once dirname(__FILE__) . "/progress_save.php";

function init_7day_progress($progress) {
    
    $seven_day['achievement_id'] = "7_day";
    $seven_day['points_actual'] = "0";
    $seven_day['finished_date'] = "";
    $seven_day['reward_claimed'] = "";
    
    $seven_day['dates'] = [];
    $seven_day['claimed_date'] = "";


    $progress['7_day'] = $seven_day;
    return $progress;
}

function add_day($user_id) {
    $reward_id = "7_day";
    $config = json_decode(file_get_contents("./config/achievements.config.json"), true);
    $progress = progress_load($user_id);
    if (!array_key_exists($reward_id, $config)) return false; // existuje
    if (!array_key_exists($reward_id, $progress)) $progress = init_7day_progress($progress);
    

    $date_today = date("d.m.Y");

    if (!in_array($date_today, $progress[$reward_id]['dates'])) $progress[$reward_id]['dates'][] = $date_today;



    if (progress_save($user_id, $progress)) return true;
    return false;
}

function get_7day_progress($user_id) {
    $reward_id = "7_day";
    $config = json_decode(file_get_contents("./config/achievements.config.json"), true);
    $progress = progress_load($user_id);
    if (!array_key_exists($reward_id, $config)) return false; // existuje
    
    $days = 0;
    $date_today = date("d.m.Y");
    $date_claimed = $progress[$reward_id]['claimed_date'];
    if (empty($date_claimed)) $date_claimed = "01.01.2020";
    $dates = $progress[$reward_id]['dates'];
    $dates = array_reverse($dates);

    $loop_n = 0;
    $date_last_in_db = 0;
    $date_today=date_create($date_today);
    $date_claimed=date_create($date_claimed);
    foreach ($dates as $date) {
        $date=date_create($date);
        if (!$date_last_in_db) $date_last_in_db = $date;

        $diff0=date_diff($date_claimed,$date);
        $diff0 = $diff0->format("%a");
        if ($diff0 == 0) break;

        $diff1=date_diff($date_today,$date);
        $diff1 = $diff1->format("%a");
        if ($diff1 == $loop_n) $days += 1;
        else break;

        if ($days >= 7) break;
        $loop_n += 1;
    }
    if ($days == 0 and $date_last_in_db->format("d.m.Y") == date('d.m.Y', time() - 1*24*60*60)) $days = 1;
    return $days;
}

function get_7day_streak($user_id) {
    $reward_id = "7_day";
    $config = json_decode(file_get_contents("./config/achievements.config.json"), true);
    $progress = progress_load($user_id);
    if (!array_key_exists($reward_id, $config)) return false; // existuje
    
    $days = 0;
    $date_today = date("d.m.Y");
    $dates = $progress[$reward_id]['dates'];
    $dates = array_reverse($dates);

    $loop_n = 0;
    $date_today=date_create($date_today);

    foreach ($dates as $date) {
        $date=date_create($date);

        $diff1=date_diff($date_today,$date);
        $diff1 = $diff1->format("%a");
        if ($diff1 == $loop_n) $days += 1;
        else break;

        $loop_n += 1;
    }
    $streak = floor($days / 7);
    return $streak;
}

function claim_7day($user_id) {
    global $conn;
    $reward_id = "7_day";
    $config = json_decode(file_get_contents("./config/achievements.config.json"), true);
    $progress = progress_load($user_id);
    if (!array_key_exists($reward_id, $config)) return false; // existuje

    if (get_7day_progress($user_id) != 7) return false;
    $reward_multiplicator = (get_7day_streak($user_id)-1) * $config[$reward_id]['reward_multiplicator'];
    if ($reward_multiplicator == 0) $reward_multiplicator = 1;
    $reward = floor($config[$reward_id]['reward'] * $reward_multiplicator);
    if ($reward > $config[$reward_id]['reward_max']) $reward = $config[$reward_id]['reward_max'];


    // nacitaj kredity
    $credit = 0;
    $sql = "SELECT credit FROM `user` WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            $credit = $row['credit'];
        }
    }
    
    // nahraj novu hodnotu kreditov
    $credit += $reward; 
    $sql = "UPDATE `user` SET credit='$credit' WHERE id='$user_id'";
    if (mysqli_query($conn, $sql)) {
        $progress[$reward_id]['claimed_date'] = $date_today = date("d.m.Y");;
        progress_save($user_id, $progress);
        return true;
    }

    return false;
}
