<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function loadCalendar($id = 0, $teacher_id = 0, $student_id = 0) {
    global $conn;

    $data = [];

    $sql = "SELECT * FROM edupage_calendar ";
    if ($id) $sql .= " WHERE id='$id'";
    if ($teacher_id and !$id) $sql .= " WHERE teacher_id='$teacher_id'";
    $student = '"' . $student_id . '"';
    if (!$teacher_id and !$id and $student_id) $sql = " WHERE student LIKE '$student'";
    $response = mysqli_query($conn, $sql);
    if (mysqli_num_rows($response) > 0) {
        while($row = mysqli_fetch_assoc($response)) $data[] = $row;
    }
    return $data;
}
