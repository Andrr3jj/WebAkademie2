<?php
$interval = 60;

if (empty($_SESSION['antispam_timer'])) $_SESSION['antispam_timer'] = time() - $interval - 1;

if (time() - $_SESSION['antispam_timer'] < $interval) {
    response("Request failed", "Trying too often");
    exit;
}

$_SESSION['antispam_timer'] = time();