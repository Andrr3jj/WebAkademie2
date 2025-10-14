<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";
include "./services/is_logged_in.php";


if (!is_logged_in()) response("Request failed", "Not logged in");
if (empty($_POST['student_id'])) response("Request failed", "Not all required data provided");

$student_id = $_POST['student_id'];

$name = "";
$surname = "";
$email_to = "";
$parent_id = "";

$sql = "SELECT name, surname, email, parent_id  FROM edupage WHERE student_id LIKE '$student_id'";
$response = mysqli_query($conn, $sql);
if (mysqli_num_rows($response) > 0) {
    while($row = mysqli_fetch_assoc($response)) {
        $name = $row['name'];
        $surname = $row['surname'];
        $email_to = $row['email'];
        $parent_id = $row['parent_id'];
    }
}


$sql = "SELECT name, surname, email FROM user WHERE id LIKE '$parent_id'";
$response = mysqli_query($conn, $sql);
if (mysqli_num_rows($response) > 0) {
    while($row = mysqli_fetch_assoc($response)) {
        $name = $row['name'];
        $surname = $row['surname'];
        $email_to = $row['email'];
    }
}

$data['name'] = $name;
$data['surname'] = $surname;

include_once "../mail/edupageCalendarUnregisterSendEmail.php";
if (sendMail_edupageCalendarUnregisterSendEmail($email_to, $data) == "Success") response("Request succesfull", "Email sent");
response("Request failed");
