<?php

function is_zapis($product_id) {
    global $conn;
    $sql = "SELECT details FROM `product` WHERE id='$product_id' AND virtuality='true'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            if (strtolower(json_decode($row['details'], true)['typ']) == "zapis") return true;
        }
    } 
    return false;
}

function get_zapis_num() {
    global $conn;
    global $user_id;

    $zapis_num = 0;
    $sql = "SELECT productsOwned FROM `user` WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            $productsOwnded = json_decode($row['productsOwned'], true);
            foreach ($productsOwnded as $product) {
                if (is_zapis($product['id'])) $zapis_num += 1;
            }
        }
    }
    return $zapis_num;
}

function vyzvy_suvisiace_s_tvojimi_piesnami() {
    /* 
    * achievementy 100 - 199
    */

    global $conn;
    global $user_id;
    global $config;
    global $progress;


    $zapis_num = get_zapis_num();

    for ($achievement_id = 100; $achievement_id <= 199; $achievement_id++) {
        // check if achievement exists
        if (!array_key_exists($achievement_id, $config)) continue;


        $points_max = $config[$achievement_id]['points_max'];

        $finished_date = "";
        $reward_claimed = "false";
        if (array_key_exists($achievement_id, $progress)) {
            $finished_date = $progress[$achievement_id]['finished_date'];
            $reward_claimed = $progress[$achievement_id]['reward_claimed'];
        
        }

        $points_actual = $zapis_num;
        $zapis_num_max = $points_max;
        if ($zapis_num >= $zapis_num_max) {
            $points_actual = $zapis_num_max;
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
