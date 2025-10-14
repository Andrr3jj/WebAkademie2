<?php

include_once dirname(__FILE__) . "/../../dbh.api.php";

include_once dirname(__FILE__) . "/progress_load.php";
include_once dirname(__FILE__) . "/progress_save.php";
include_once dirname(__FILE__) . "/../../helitime/services/get_helitime_history.php";


function claim_reward($user_id, $reward_id) {
    global $conn;

    $config = json_decode(file_get_contents("./config/achievements.config.json"), true);
    $progress = progress_load($user_id);
    if (!array_key_exists($reward_id, $progress)) return false; // existuje
    if (!array_key_exists($reward_id, $config)) return false; // existuje
    if (empty($progress[$reward_id]['finished_date'])) return false; // bolo splenene
    if ($progress[$reward_id]['reward_claimed'] == "true") return false; // bola vybrana odmena


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
    $credit += $config[$reward_id]['reward']; 
    $sql = "UPDATE `user` SET credit='$credit' WHERE id='$user_id'";
    if (mysqli_query($conn, $sql)) {
        $progress[$reward_id]['reward_claimed'] = "true";
        progress_save($user_id, $progress);
        
        $helitime_history = get_helitime_history($user_id);
        $helitime_history .= "|claim_reward.php " . date("d.m.Y s:i:H") . " -> claim reward_id:" . $reward_id;
        $sql = "UPDATE `user` SET helitime_history='$helitime_history' WHERE id='$user_id'";
        mysqli_query($conn, $sql);

        return true;
    }
    return false;
}
