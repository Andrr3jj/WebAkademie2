<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function search($string) {
    global $conn;
    $data = [];

    $sql = "SELECT id, name, surname FROM `user` WHERE name LIKE '%$string%' OR surname LIKE '%$string%'"; 
    if (str_contains($string, " ")) {
        $name = explode(" ", $string)[0];
        $surname = explode(" ", $string)[1];
        $sql = "SELECT id, name, surname FROM `user` WHERE name LIKE '%$name%' OR surname LIKE '%$surname%'"; 
    }
    $sql .= " LIMIT 10";
    
    $query = mysqli_query($conn, $sql);
    if (mysqli_num_rows($query) > 0) {
        while($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        } 
    }

    return $data;
}
