<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function createCalendar() {
    global $conn;

    $teacher_id = $_SESSION['id'];

    $formOfStudy = "";
    if (!empty($_POST['formOfStudy'])) $formOfStudy = $_POST['formOfStudy']; 
    $availability_day = "";
    if (isset($_POST['availability_day'])) $availability_day = $_POST['availability_day']; 
    $availability_hour = "";
    if (isset($_POST['availability_hour'])) $availability_hour = $_POST['availability_hour']; 
    $student = "";
    if (!empty($_POST['student'])) $student = $_POST['student']; 

    $sql = "INSERT INTO edupage_calendar (teacher_id, formOfStudy, availability_day, availability_hour, student) 
    VALUES ('$teacher_id', '$formOfStudy', '$availability_day', '$availability_hour', '$student')";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}
