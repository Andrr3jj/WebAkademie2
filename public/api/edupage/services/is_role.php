<?php

function is_role($role = "edupageRegistrationsManager") {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['logged_in'])) return false;
    if (in_array($role, $_SESSION['roles'])) return true;
    return false;
}
