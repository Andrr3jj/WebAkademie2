<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function loadStudentsUnassigned() {
    global $conn;

    $allStudents = [];
    $unassignedStudents = [];
    $studentsWoId = [];
    
    $sql = "SELECT student_id FROM edupage ";
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            if (!in_array($row['student_id'], $allStudents)) $allStudents[] = $row['student_id'];
        }
    }


    foreach ($allStudents as $student) {
        $student_search = '"' . $student . '"';
        $sql = "SELECT * FROM edupage_calendar WHERE student LIKE '%$student_search%'";
        $res = mysqli_query($conn, $sql);
        if (mysqli_num_rows($res) == 0) {
            $sql = "SELECT * FROM edupage WHERE student_id='$student'";
            $res = mysqli_query($conn, $sql);
            if (mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
                    if (!in_array($row, $unassignedStudents)) $unassignedStudents[] = $row;
                }
            }
        }
    }


    
    $sql = "SELECT parent_id FROM edupage WHERE student_id=''";
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            if (!in_array($row['parent_id'], $studentsWoId)) $studentsWoId[] = $row['parent_id'];
        }
    }


    foreach ($studentsWoId as $student) {
        $student_search = '"' . $student . '"';
        $sql = "SELECT * FROM edupage_calendar WHERE student LIKE '%$student_search%'";
        $res = mysqli_query($conn, $sql);
        if (mysqli_num_rows($res) == 0) {
            $sql = "SELECT * FROM edupage WHERE parent_id='$student'";
            $res = mysqli_query($conn, $sql);
            if (mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
                    if (!in_array($row, $unassignedStudents)) $unassignedStudents[] = $row;
                }
            }
        }
    }

    return $unassignedStudents;
}
