<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";
include "./services/is_logged_in.php";
include "./services/is_role.php";




if (!is_logged_in() OR !is_role("edupageRegistrationsManager")) response("Request failed", "Not logged in");
if (empty($_POST['student_id'])) response("Request failed", "Not all required data provided");

$payment_id = $_POST['payment_id'];
$DB = [];
$data = [];

$sql = "SELECT * FROM edupage_payment WHERE id='$payment_id'";
$res = mysqli_query($conn, $sql);
if (!$res) response("Request failed");
while ($row = mysqli_fetch_assoc($res)) $DB['edupage_payment'] = $row;

$user_id = $DB['edupage_payment']['user_id'];


$userRow = get1DB("user", "id='$user_id'");
$user_name = $userRow ? ($userRow['name'] . " " . $userRow['surname']) : '';

$search = '"' . $user_id . '"';
$calRow = get1DB("edupage_calendar", "student LIKE '%$search%'");
$teacher_id = $calRow ? $calRow['teacher_id'] : null;

$teacherRow = $teacher_id ? get1DB("user", "id='$teacher_id'") : null;
$teacher = $teacherRow ? ($teacherRow['name'] . " " . $teacherRow['surname']) : '';

$studyRow = get1DB("edupage", "student_id='$user_id'");
$placeOfStudy = $studyRow ? $studyRow['placeOfStudy'] : '';

$data['qr'] = $DB['edupage_payment']['qr'];
$data['payment_note'] = $user_name . ", " . $teacher . ", " . $placeOfStudy;
$data['lesson_count'] = "4";


$month = null;
$year  = null;

if (!empty($DB['edupage_payment']['date'])) {
    // ocakavany format v DB: 25.09.2025
    $dt = DateTime::createFromFormat('d.m.Y', $DB['edupage_payment']['date']);
    if ($dt) {
        $month = (int)$dt->format('m');
        $year  = (int)$dt->format('Y');
    }
}

$months = ['január', 'február', 'marec', 'apríl', 'máj', 'jún', 'júl', 'august', 'september', 'október', 'november', 'december'];
if ($month !== null) $data['month'] = $months[$month-1];
if ($year !== null)  $data['year']  = $year;

// 15. nasledujuceho mesiaca (s prechodom roku)
if ($month !== null) {
    $pbMonth = $month;
    $pbYear  = $year;
    if ($pbMonth > 12) { $pbMonth = 1; if ($pbYear !== null) $pbYear++; }
    if (strlen($pbMonth == 1)) $pbMonth = "0" . $pbMonth;
    $data['pay_before'] = '20. ' . $months[$pbMonth-1] . ' ' . $pbYear;
}

print_r($data['pay_before']);

$data['price'] = $DB['edupage_payment']['price'];


$sql = "SELECT * FROM user WHERE id='$user_id'";
$res = mysqli_query($conn, $sql);
if (!$res) response("Request failed");
while ($row = mysqli_fetch_assoc($res)) $DB['user'] = $row;

$data['name'] = $DB['user']['name'];
$data['surname'] = $DB['user']['surname'];
$email_to = $DB['user']['email'];


include_once "../mail/edupagePaymentRequest.php";
if (!empty($data) and sendMail_edupagePaymentRequestSendEmail($email_to, $data) == "Success") {
    $sql = "UPDATE edupage_payment SET email_payment_request='true' WHERE id=$payment_id";
    mysqli_query($conn, $sql);
    response("Request succesfull", "Email sent");
}
response("Request failed");

function get1DB($db, $where = "1 = 1") {
    global $conn;

    $user_name = "";
    $sql = "SELECT * FROM $db WHERE " . $where;
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) > 0) {
        while($row = mysqli_fetch_assoc($res)) return $row;
    }
    return false;
}

