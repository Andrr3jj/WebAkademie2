<?php

function is_logged_in() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['logged_in'])) return true;
    return false;
}
