<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function createCalendarCustom() {
    global $conn;

    $teacher_id = $_SESSION['id'];

    $data = "";
    if (!empty($_POST['data'])) $data = $_POST['data']; 
    $teacher_id = "";
    if (!empty($_POST['teacher_id'])) $teacher_id = $_POST['teacher_id']; 

    $sql = "INSERT INTO edupage_calendar_custom (teacher_id, data) 
    VALUES ('$teacher_id', '$data')";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}



function loadCalendarCustom($teacher_id = 0) {
    global $conn;

    $data = [];

    $sql = "SELECT * FROM edupage_calendar_custom";
    if ($teacher_id) $sql .= " WHERE teacher_id='$teacher_id'";

    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) > 0) {
        while($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
    }

    return $data;
}



function editCalendarCustom() {
    global $conn;

    $id = $_POST['id'];

    $data = "";
    if (!empty($_POST['data'])) $data = $_POST['data']; 
    $teacher_id = "";
    if (!empty($_POST['teacher_id'])) $teacher_id = $_POST['teacher_id']; 
    
    $delete = false;
    if (!empty($_POST['delete'])) $delete = $_POST['delete']; 


    $sql = "UPDATE edupage_calendar_custom SET 
        teacher_id='$teacher_id',
        data='$data'
        WHERE id=$id";
    if ($delete) $sql = "DELETE FROM edupage_calendar_custom WHERE id=$id";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}
