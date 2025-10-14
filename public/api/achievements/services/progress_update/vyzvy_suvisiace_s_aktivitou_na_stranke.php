<?php

function get_user() {
    global $conn;
    global $user_id;

    $sql = "SELECT * FROM `user` WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            return $row;
        }
    }
    return null;
}




function vyzvy_suvisiace_s_aktivitou_na_stranke() {
    /* 
    * achievementy 0 - 99
    */

    global $conn;
    global $user_id;
    global $config;
    global $progress;


    $user = get_user();

    for ($achievement_id = 400; $achievement_id <= 499; $achievement_id++) {
        // check if achievement exists
        if (!array_key_exists($achievement_id, $config)) continue;


        $points_max = $config[$achievement_id]['points_max'];

        $finished_date = "";
        $reward_claimed = "false";
        if (array_key_exists($achievement_id, $progress)) {
            $finished_date = $progress[$achievement_id]['finished_date'];
            $reward_claimed = $progress[$achievement_id]['reward_claimed'];
        
        }

        $points_actual = 0;

        // profilova fotka
        if ($config[$achievement_id]['name'] == "Nahraj si profilovú fotku") {
            if (!str_contains($user['profilePhotoUrl'], "default/") and !empty($user['profilePhotoUrl'])){
                if ($finished_date == "") $finished_date = date("d.m.Y");
                $points_actual = $points_max;
            } 
        }

        // vyplň krátky formulár
        if ($config[$achievement_id]['name'] == "Vyplň krátky formulár") {
            if (!empty($user['formular'])){
                if ($finished_date == "") $finished_date = date("d.m.Y");
                $points_actual = $points_max;
            } 
        }

        // prihlás sa na odber noviniek
        if ($config[$achievement_id]['name'] == "Prihlás sa na odber noviniek") {
            if ($user['ads'] == "true"){
                if ($finished_date == "") $finished_date = date("d.m.Y");
                $points_actual = $points_max;
            } 
        }

        
        $progress[$achievement_id] = [
            "achievement_id" => $achievement_id,
            "points_actual" => $points_actual,
            "finished_date" => $finished_date,
            "reward_claimed" => $reward_claimed
        ];
    } 
}
