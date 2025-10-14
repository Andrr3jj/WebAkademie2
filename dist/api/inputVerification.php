<?php

function verifyName($name) { 
    if (strlen($name) == 0) return false;
    if (strlen($name) > 200) return false;
    return true;
}

function verifyEmail($email) {
    if (strlen($email) == 0) return false;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
    return true;
}
