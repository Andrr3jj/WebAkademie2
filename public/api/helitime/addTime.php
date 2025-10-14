<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");

session_start();
include "../dbh.api.php";
include "./services/response.php";

include "./services/is_logged_in.php";
include "./services/get_time.php";
include "./services/set_time.php";

if (!is_logged_in()) response("Request failed", "Not logged in");
$user_id = $_SESSION['id'];

$time = get_time(-1); 
if (empty($time)) $time = 0;
else $time = $time[array_key_first($time)];


if (empty($_SESSION['antispam_addTime.php'])) $_SESSION['antispam_addTime.php'] = time() - 10;
if ($_SESSION['antispam_addTime.php'] < time()) {
    $config = json_decode(file_get_contents("./config/helitime.config.json"), true);

    $time_add = $config['helitime_credit_per_timeout'];


    // new day
    if (empty($_SESSION['helitime_day']) or $_SESSION['helitime_day'] == date("j") - 1 or $_SESSION['helitime_day'] - 1 == 0) {
        $_SESSION['helitime_day'] = date("j");
        $_SESSION['helitime_start'] = time();
        $_SESSION['helitime_time_aquired_today'] = 0;
    } 
    if (empty($_SESSION['helitime_start'])) $_SESSION['helitime_start'] = time();
    if (empty($_SESSION['helitime_time_aquired_today'])) $_SESSION['helitime_time_aquired_today'] = 0;

    /*
    // first x hours play time
    if ($_SESSION['helitime_start'] > time() - $config['helitime_first_play_time_seconds']) $time_add *= $config['helitime_first_play_time_multiplicator'];

    // special event0
    $current_hour = date("G");
    if ($current_hour > $config['helitime_event0_start_time_hour'] and $current_hour < $config['helitime_event0_end_time_hour']) $time_add *= $config['helitime_event0_multiplicator'];
    */


    // first x hours play time or special event0
    $current_hour = date("G");
    if ($_SESSION['helitime_start'] > time() - $config['helitime_first_play_time_seconds']) $time_add *= $config['helitime_first_play_time_multiplicator'];
    else if ($current_hour > $config['helitime_event0_start_time_hour'] and $current_hour < $config['helitime_event0_end_time_hour']) $time_add *= $config['helitime_event0_multiplicator'];

    // max day limit
    if ($_SESSION['helitime_time_aquired_today'] > $config['helitime_daily_limit']) $time_add = $config['helitime_daily_limit_reached_points'];
    $_SESSION['helitime_time_aquired_today'] += $time_add;

    $time += $time_add;
    set_time($user_id, $time);
    $_SESSION['antispam_addTime.php'] = time() + $config['helitime_timeout_seconds'];
    response("Request succesfull");
}
response("Request failed", "Too often");
