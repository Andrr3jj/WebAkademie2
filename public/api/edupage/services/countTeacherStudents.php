<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function countTeacherStudents($id) {
    global $conn;


    $sql = "SELECT * FROM edupage WHERE teacher_id='$id' and status='zaradený'";
    $response = mysqli_query($conn, $sql);
    if (mysqli_num_rows($response) == 0) return $data;
    while($row = mysqli_fetch_assoc($response)) {
        if (!isset($data[$row['formOfStudy']])) $data[$row['formOfStudy']] = 0; 
        $data[$row['formOfStudy']] += 1;
    }

    return $data;
}
