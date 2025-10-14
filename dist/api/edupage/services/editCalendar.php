<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function editCalendar() {
    global $conn;

    $id = $_POST['id'];

    $formOfStudy = "";
    if (!empty($_POST['formOfStudy'])) $formOfStudy = $_POST['formOfStudy']; 
    $availability_day = "";
    if (isset($_POST['availability_day'])) $availability_day = $_POST['availability_day']; 
    $availability_hour = "";
    if (isset($_POST['availability_hour'])) $availability_hour = $_POST['availability_hour']; 
    $student = "";
    if (!empty($_POST['student'])) $student = $_POST['student']; 
    
    $delete = false;
    if (!empty($_POST['delete'])) $delete = $_POST['delete']; 


    $sql = "UPDATE edupage_calendar SET 
        formOfStudy='$formOfStudy',
        availability_day='$availability_day',
        availability_hour='$availability_hour',
        student='$student'
        WHERE id=$id";
    if ($delete) $sql = "DELETE FROM edupage_calendar WHERE id=$id";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}
