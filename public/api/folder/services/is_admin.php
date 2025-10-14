<?php

function is_admin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['is_admin']) and $_SESSION['is_admin'] == true) return true;
    return false;
}
