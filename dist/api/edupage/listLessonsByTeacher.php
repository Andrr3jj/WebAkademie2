<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";
include "./services/is_logged_in.php";

include "./services/payment.php";



if (!is_logged_in()) response("Request failed", "Not logged in");

if (empty($_GET['teacher_id']) OR empty($_GET['year']) OR empty($_GET['month']) OR empty($_GET['day'])) response("Request failed", "DATA");
$teacher_id = $_GET['teacher_id'];
$year = $_GET['year'];
$month = $_GET['month'];
$day = $_GET['day'];

$data = [];


//get edupage lessons
$date_search = $month . "." . $year;
$sql = "SELECT * FROM edupage_lesson WHERE teacher_id='$teacher_id' and date LIKE '%$date_search'";
$res = mysqli_query($conn, $sql);
if (mysqli_num_rows($res) == 0) response("Request succesfull", []);
$edupage_lessons = [];
while($row = mysqli_fetch_assoc($res)) {
    if (date("w", strtotime($row['date'])) == $day) $edupage_lessons[] = $row;
}


// Počet dní v mesiaci, ktoré sú daným dňom v týždni (napr. počet utorkov v mesiaci)
$targetDay = intval($day); // 0=nedeľa, 1=pondelok, ...
$targetMonth = intval($month);
$targetYear = intval($year);
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $targetMonth, $targetYear);
$matchingDays = 0;
for ($d = 1; $d <= $daysInMonth; $d++) {
    $w = date('w', strtotime(sprintf('%04d-%02d-%02d', $targetYear, $targetMonth, $d)));
    if ($w == $targetDay) $matchingDays++;
}
$data['count'] = $matchingDays;




// Odstránenie duplicít lekcií pre rovnakého študenta na rovnaký dátum (nezáleží na čase)
$student_data = [];
$grouped_lessons = [];
foreach ($edupage_lessons as $edupage_lesson) {
    $lesson_students = json_decode($edupage_lesson['student'], true);
    foreach ($lesson_students as $lesson_student) {
        $student_id = $lesson_student['student_id'];
        $lesson_start = $edupage_lesson['hour'];
        $date = $edupage_lesson['date'];
        $status = $edupage_lesson['status'];
        $key = $student_id . '|' . $date;

        $student_data_single = [];
        $student_data_single['lesson_id'] = $edupage_lesson['id'];
        $student_data_single['lesson_start'] = $lesson_start;
        $student_data_single['status'] = $status;
        $student_data_single['attendance_status'] = $lesson_student['attendance_status'];
        $student_data_single['date'] = $date;
    $user_row = get1DB("user", "id='$student_id'");
    $student_data_single['id'] = $student_id;
    $student_data_single['name'] = $user_row['name'];
    $student_data_single['surname'] = $user_row['surname'];
    $student_data_single['profilePhotoUrl'] = isset($user_row['profilePhotoUrl']) ? $user_row['profilePhotoUrl'] : null;
        // Pridaj formOfStudy cez calendar_id
        $calendar_id = $edupage_lesson['calendar_id'];
        $formOfStudy = null;
        if ($calendar_id) {
            $calRow = get1DB("edupage_calendar", "id='$calendar_id'");
            if ($calRow && isset($calRow['formOfStudy'])) {
                $formOfStudy = $calRow['formOfStudy'];
            }
        }
        $student_data_single['formOfStudy'] = $formOfStudy;
        $student_data_single['price'] = "Payment not found";
        $student_data_single['paid'] = "Payment not found";
        $student_data_single['email_payment_request'] = "Payment not found";
        $student_data_single['email_payment_notice'] = "Payment not found";
        $payment_id = findPayment($student_id, $month, $year);
        if ($payment_id) {
            $payment = loadPayment($payment_id);
            $student_data_single['price'] = $payment['price'];
            $student_data_single['paid'] = $payment['paid'];
            $student_data_single['email_payment_request'] = $payment['email_payment_request'];
            $student_data_single['email_payment_notice'] = $payment['email_payment_notice'];
        }
        $grouped_lessons[$key][] = $student_data_single;
    }
}

// Pre každý deň a študenta vyber len jednu lekciu podľa pravidiel
foreach ($grouped_lessons as $key => $lessons) {
    // preferuj absolved/cancelled_admin, inak planned
    $chosen = null;
    foreach ($lessons as $lesson) {
        if ($lesson['status'] === 'absolved' || $lesson['status'] === 'cancelled_admin') {
            $chosen = $lesson;
            break;
        }
    }
    if (!$chosen) {
        // ak nie je žiadna absolved/cancelled_admin, vezmi prvú planned
        foreach ($lessons as $lesson) {
            if ($lesson['status'] === 'planned') {
                $chosen = $lesson;
                break;
            }
        }
    }
    if (!$chosen) {
        // fallback: ak by nebola ani planned, vezmi prvú
        $chosen = $lessons[0];
    }
    $student_data[] = $chosen;
}


// ZJEDNOTENIE ŠTUDENTOV A PRIDANIE POČTOV LEKCIÍ
$students = [];
foreach ($student_data as $row) {
    $key = trim($row['name']) . '|' . trim($row['surname']);
    if (!isset($students[$key])) {
        $students[$key] = [
            'id' => isset($row['id']) ? $row['id'] : null,
            'name' => $row['name'],
            'surname' => $row['surname'],
            'profilePhotoUrl' => isset($row['profilePhotoUrl']) ? $row['profilePhotoUrl'] : null,
            'price' => $row['price'],
            'paid' => $row['paid'],
            'email_payment_request' => $row['email_payment_request'],
            'email_payment_notice' => $row['email_payment_notice'],
            'countAbsolvedLessons' => 0,
            'countAdminCancelledLesson' => 0,
            'countParentCancelledLesson' => 0,
            'lesson_ids' => [],
            'lesson_starts' => [],
            'statuses' => [],
            'attendance_statuses' => [],
            'formOfStudy' => null,
        ];
    }
    if (isset($row['lesson_id'])) {
        $students[$key]['lesson_ids'][] = $row['lesson_id'];
    }
    if (isset($row['lesson_start'])) {
        $students[$key]['lesson_starts'][] = $row['lesson_start'];
    }
    if (isset($row['status'])) {
        $students[$key]['statuses'][] = $row['status'];
    }
    if (array_key_exists('attendance_status', $row)) {
        $students[$key]['attendance_statuses'][] = $row['attendance_status'];
    }
    if (isset($row['formOfStudy']) && $row['formOfStudy'] !== null) {
        $students[$key]['formOfStudy'] = $row['formOfStudy'];
    }
    if ($row['attendance_status'] === false || $row['attendance_status'] === 0 || $row['attendance_status'] === '0') {
        $students[$key]['countAbsolvedLessons']++;
    } elseif (is_null($row['attendance_status'])) {
        $students[$key]['countAdminCancelledLesson']++;
    } elseif ($row['attendance_status'] === true || $row['attendance_status'] === 1 || $row['attendance_status'] === '1') {
        $students[$key]['countParentCancelledLesson']++;
    }
}

$data['student_data'] = array_values($students);

response("Request succesfull", $data);

