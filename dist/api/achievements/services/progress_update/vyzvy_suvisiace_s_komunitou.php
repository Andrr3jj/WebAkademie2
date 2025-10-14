<?php


function invited_own_zapis() {
    global $conn;
    global $user_id;

    $invited = get_invited();
    $pass_count = 0;
    foreach ($invited as $inv) {
        $sql = "SELECT products_owned FROM `user` WHERE id='$inv'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $products_owned = json_decode($row['products_owned'], true);
                foreach ($products_owned as $product_owned) {
                    if (is_zapis($product_owned['id'])) $pass_count += 1;
                }
            }
        }
        
    }
    return $pass_count;
}


function invited_helitime_2h() {
    global $conn;
    global $user_id;

    $invited = get_invited();
    $pass_count = 0;
    foreach ($invited as $inv) {
        $sql = "SELECT helitime FROM `user` WHERE id='$inv' and helitime>'120'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) $pass_count += 1;
    }
    return $pass_count;
}

function invited_spend_10e() {
    global $conn;
    global $user_id;

    $invited = get_invited();
    foreach ($invited as $inv) {
        $sql = "SELECT id FROM `order` WHERE user_id='$inv' AND price>'999'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) return true;
    }
    return false;
}

function get_invited() {
    global $conn;
    global $user_id;

    $data = [];

    $sql = "SELECT id FROM `user` WHERE invited_by='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data = $row['id'];
        }
    }
    return $data;
}


function get_invited_num() {
    global $conn;
    global $user_id;

    $sql = "SELECT id FROM `user` WHERE invited_by='$user_id'";
    $result = mysqli_query($conn, $sql);
    return mysqli_num_rows($result);
}

function vyzvy_suvisiace_s_komunitou() {
    /* 
    * achievementy 300 - 399
    */

    global $conn;
    global $user_id;
    global $config;
    global $progress;


    $invited_num = get_invited_num();

    for ($achievement_id = 300; $achievement_id <= 399; $achievement_id++) {
        // check if achievement exists
        if (!array_key_exists($achievement_id, $config)) continue;

        $points_actual = 0;
        $points_max = $config[$achievement_id]['points_max'];

        $finished_date = "";
        $reward_claimed = "false";
        if (array_key_exists($achievement_id, $progress)) {
            $finished_date = $progress[$achievement_id]['finished_date'];
            $reward_claimed = $progress[$achievement_id]['reward_claimed'];
        
        }


        // Pozvi priateľa a učte sa spolu
        if ($config[$achievement_id]['name'] == "Pozvi priateľa a učte sa spolu") {
            if ($invited_num >= 1) $points_actual += 0.5;
            if (invited_spend_10e()) $points_actual += 0.5;

            if ($points_actual == 1){
                if ($finished_date == "") $finished_date = date("d.m.Y");
                $points_actual = $points_max;
            } 
        }

        // Heligónka v partii
        if ($config[$achievement_id]['name'] == "Heligónka v partii") {
            $points_actual = $invited_num * 0.5;
            if ($points_actual > 1.5) $points_actual = 1.5; // max 1.5 bodu (3 hraci)
            
            $points_actual += invited_helitime_2h() * 0.5;
            if ($points_actual > 3) $points_actual = 3; // max 3 body = points_max

            if ($points_actual == 3){
                if ($finished_date == "") $finished_date = date("d.m.Y");
                $points_actual = $points_max;
            } 
        }

        // Rozšír komunitu
        if ($config[$achievement_id]['name'] == "Rozšír komunitu") {
            $points_actual = $invited_num * 0.5;
            if ($points_actual > 2.5) $points_actual = 2.5; // max 2.5 bodu (5 hraci)
            
            $points_actual += invited_own_zapis() * 0.5;
            if ($points_actual > 5) $points_actual = 5; // max 5 body = points_max

            if ($points_actual == 5){
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
