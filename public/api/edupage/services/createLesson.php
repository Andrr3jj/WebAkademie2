<?php

include_once dirname(__DIR__) . "../../dbh.api.php";
include_once dirname(__DIR__) . "./loadCalendar.php";

function createLesson() {
    global $conn;

    $calendar = loadCalendar();
    foreach ($calendar as $c_day) {
        $calendar_id = $c_day['id'];
        $teacher_id = $c_day['teacher_id'];

        $student = [];
        foreach ($c_day['student'] as $s) {
            $student_data = [
                'student_id' => $s,
                'stars' => 0,
                'note' => 0,
                'attendance_status' => 0,
                'played' => 0
            ];
            $student[] = $student_data;
        }

        $dayMap = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $date = date("d.m.Y", strtotime('next ' . $dayMap[$c_day['availability_day']]));

        $hour = $c_day['availability_hour'];
        $status = "planned";


        $sql = "INSERT INTO edupage_lesson (calendar_id, teacher_id, student, date, hour, status) 
        VALUES ('$calendar_id', '$teacher_id', '$student', '$date', '$hour', '$status')";
        if (!mysqli_query($conn, $sql)) return false;
    }
    return false;
}
