<?php

include_once('../../dbh.api.php');

function generateNumber() {
    $nums = "123456789";
    return $nums[mt_rand(0, strlen($nums)-1)];
}

function generateCharacter() {
    $chars = "ABCDEFGHIJKLMNPRSTUVWXYZ";
    return $chars[mt_rand(0, strlen($chars)-1)];
}

function generateCode($code = "") {
    //X00X-XX0X-00XX 
    global $conn;
    
    $arr = [];
    $sql = "SELECT code FROM gift_card";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) while($row = mysqli_fetch_assoc($result)) $arr[] = $row["code"];
    
    if (in_array($code, $arr) and $code != "") return false;
    
    
    $res = $code;


    switch (strlen($res)) { 
        case 0: $res .= generateCharacter(); 
        case 1: $res .= generateNumber();
        case 2: $res .= generateNumber();
        case 3: $res .= generateCharacter();
        case 4: $res .= "-";
        case 5: $res .= generateCharacter();
        case 6: $res .= generateCharacter();
        case 7: $res .= generateNumber();
        case 8: $res .= generateCharacter();
        case 9: $res .= "-";
        case 10: $res .= generateNumber();
        case 11: $res .= generateNumber();
        case 12: $res .= generateCharacter();
        case 13: $res .= generateCharacter();
    }


    if (in_array($res, $arr))  return generateCode($code);
    return $res;
}
