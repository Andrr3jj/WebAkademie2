<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");

session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/progress_load.php";
include "./services/progress_save.php";

if (!is_logged_in()) response("Request failed", "Not logged in");
if (empty($_SESSION['id'])) response("Request failed", "Wrong login");
$user_id = $_SESSION['id'];
$config = json_decode(file_get_contents("./config/achievements.config.json"), true);

$progress = progress_load($user_id);

//achievements 0 - 99
include "./services/progress_update/vyzvy_suvisiace_s_ucenim.php";
vyzvy_suvisiace_s_ucenim();

//achievements 100 - 199
include "./services/progress_update/vyzvy_suvisiace_s_tvojimi_piesnami.php";
vyzvy_suvisiace_s_tvojimi_piesnami();

//achievements 200 - 299
include "./services/progress_update/vyzvy_suvisiace_s_naucnimy_videami.php";
vyzvy_suvisiace_s_naucnimy_videami();


//achievements 300 - 399
include "./services/progress_update/vyzvy_suvisiace_s_komunitou.php";
vyzvy_suvisiace_s_komunitou();


//achievements 400 - 499
include "./services/progress_update/vyzvy_suvisiace_s_aktivitou_na_stranke.php";
vyzvy_suvisiace_s_aktivitou_na_stranke();

progress_save($user_id, $progress);

$data = [];

foreach($config as $key => $config_single){
    $config_single['achievement_id'] = $key;
    $config_single['points_actual'] = "0";
    $config_single['finished_date'] = "";
    $config_single['reward_claimed'] = "false";
    if (array_key_exists($key, $progress)) {
        $config_single['achievement_id'] = $progress[$key]['achievement_id'];
        $config_single['points_actual'] = $progress[$key]['points_actual'];
        $config_single['finished_date'] = $progress[$key]['finished_date'];
        $config_single['reward_claimed'] = $progress[$key]['reward_claimed'];
    }
    $data[$key] = $config_single;
}

response("Request succesfull", $data);
