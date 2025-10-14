<?php
$interval = 1;
$max_antispam_count = 3;

if (empty($_SESSION['antispam_timer'])) $_SESSION['antispam_timer'] = time() - $interval - 1;
if (empty($_SESSION['antispam_count'])) $_SESSION['antispam_count'] = 0;

if (time() - $_SESSION['antispam_timer'] < $interval) {
    $_SESSION['antispam_count'] += 1;
    if ($_SESSION['antispam_count'] > $max_antispam_count) {
        $_SESSION['antispam_count'] = 0;
        //response("Request failed", "Trying too often");
        //exit;
    }
} else {
    $_SESSION['antispam_count'] = 0;
}

$_SESSION['antispam_timer'] = time();