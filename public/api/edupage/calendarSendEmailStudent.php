<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";
include "./services/is_logged_in.php";

//?include "./services/assignCalendar.php";

if (!is_logged_in()) response("Request failed", "Not logged in");
if (empty($_POST['student_id'])) response("Request failed", "Not all required data provided");

$student_id = $_POST['student_id'];





$data['calendar']['availability_day'] = " ";
$data['calendar']['availability_hour'] = " ";
$search = '"' . $student_id . '"';
$sql = "SELECT * FROM edupage_calendar WHERE student LIKE '%$search%'";
$response = mysqli_query($conn, $sql);
if (mysqli_num_rows($response) > 0) {
    while($row = mysqli_fetch_assoc($response)) $data['calendar'] = $row;
}
$data['calendar']['availability_day'] = num2day($data['calendar']['availability_day']);
$data['calendar']['availability_hour'] = substr($data['calendar']['availability_hour'], 0, -2) . ":" . substr($data['calendar']['availability_hour'], -2);
$data['info'] = "";
$sql = "SELECT * FROM edupage WHERE student_id='$student_id'";
$response = mysqli_query($conn, $sql);
if (mysqli_num_rows($response) > 0) {
    while($row = mysqli_fetch_assoc($response)) $data['info'] = $row;
}
if (!empty($_POST['placeOfStudy'])) $data['info']['placeOfStudy'] = $_POST['placeOfStudy'];
$data['custom'] = [];
$parent_id=$data['info']['parent_id'];
$data['custom']['parentName'] = "";
$parent_email = "";
$teacher_id=$data['info']['teacher_id'];
$sql = "SELECT * FROM user WHERE id='$parent_id'";
$response = mysqli_query($conn, $sql);
if (mysqli_num_rows($response) > 0) {
    while($row = mysqli_fetch_assoc($response)) {
        $data['custom']['parentName'] = $row['name'];
        $parent_email = $row['email'];
    }
}
$data['custom']['teacherName'] = "";
$data['custom']['teacherEmail'] = "";
$data['custom']['teacherPhone'] = "";
$sql = "SELECT * FROM user WHERE id='$teacher_id'";
$response = mysqli_query($conn, $sql);
if (mysqli_num_rows($response) > 0) {
    while($row = mysqli_fetch_assoc($response)) {
        $data['custom']['teacherName'] = $row['name'] . " " . $row['surname'];
        $data['custom']['teacherEmail'] = $row['email'];
        $data['custom']['teacherPhone'] = $row['phone'];
    } 
}


$email_to = $data['info']['email'];
if ($parent_email) $email_to = $parent_email;


include_once "../mail/edupageCalendarSendEmail.php";
if (!empty($data['info']['email']) and sendMail_edupageCalendarSendEmail($email_to, $data) == "Success") response("Request succesfull", "Email sent");
response("Request failed");


function num2day($num){
    switch ($num) {
        case 0:
            return "Pondelok";
        case 1:
            return "Utorok";
        case 2:
            return "Streda";
        case 3:
            return "Štvrtok";
        case 4:
            return "Piatok";
        case 5:
            return "Sobota";
        case 6:
            return "Nedeľa";
    }
}
