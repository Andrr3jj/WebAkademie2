<?php

include_once dirname(__DIR__) . "../../dbh.api.php";
include_once dirname(__DIR__) . "/services/loadCalendar.php";

function createPayment($user_id, $month, $year) {
    global $conn;

    $search_0 = '"' . $user_id . '"';
    $search_1 = ":" . $user_id . ",";
    $search_date = $month . ".".$year;
    $sql = "SELECT * FROM edupage_lesson WHERE (student LIKE '%$search_0%' OR student LIKE '%$search_1%') AND date LIKE '%$search_date%'";
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) == 0 ) return false;

    $lesson = [];
    $price = 0;
    $status = "PENDING";
    while($edupage_lesson = mysqli_fetch_assoc($res)) {
        $price_this = 16;
        if (substr_count($edupage_lesson['student'], "student_id") == 2) $price_this = 14;

        if (!in_array($edupage_lesson['id'], $lesson)) {
            $lesson[] = $edupage_lesson['id'];
            $price += $price_this;
        } 
    }
    $lesson_JSON = json_encode($lesson);
    $price = number_format((float)$price, 2, '.', '');

    $date = date("d.m.Y");
    if ($month) $date = date("d") . "." . $month . "." . $year; 



    $search = '"' . $user_id . '"';
    $calRow = get1DB("edupage_calendar", "student LIKE '%$search%'");
    $teacher_id = $calRow ? $calRow['teacher_id'] : null;

    $user_name = get1DB("user", "id='$user_id'")['name'] . " " . get1DB("user", "id='$user_id'")['surname'];
    $teacher = get1DB("user", "id='$teacher_id'")['name'] . " " . get1DB("user", "id='$teacher_id'")['surname'];;
    $placeOfStudy = get1DB("edupage", "student_id='$user_id'")['placeOfStudy'];


    $qr = "https://heligonkovaakademia.sk/api/edupage/qr/qr.html?price=".$price."&info=".$user_name.", ".$teacher . ", " . $placeOfStudy;


    $sql = "INSERT INTO edupage_payment (user_id, lesson, date, price, status, qr) 
            VALUES ('$user_id', '$lesson_JSON', '$date', '$price', '$status', '$qr')";
    
    if (mysqli_query($conn, $sql)) return true;
    return false;
}



function preparePayment($user_id, $month, $year, $loop = 0) {
    global $conn;
    $user_id = 607;

    $data = [];
    if ($loop > 2) return $data; // breaks incursion 

    $payment_id = findPayment($user_id, $month, $year);
    if ($payment_id) return false; // exists


    $search_0 = '"' . $user_id . '"';
    $search_1 = ":" . $user_id . ",";
    $sql = "SELECT * FROM edupage_calendar WHERE student LIKE '%$search_0%' OR student LIKE '%$search_1%'";
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) == 0 ) return false;

    $calendar = [];
    $teacher_id = 0;
    $price = 0;
    $status = "PENDING";
    while($edupage_calendar = mysqli_fetch_assoc($res)) {
        if (!in_array($edupage_calendar['id'], $calendar)) $calendar[] = $edupage_calendar['id'];
        $price = 16;
        if (str_contains($edupage_calendar['student'], ",")) $price = 14;
        $teacher_id = $edupage_calendar['teacher_id'];
    }
    $calendar_JSON = json_encode($calendar);
    $price = number_format((float)$price, 2, '.', '');

    $date = date("d.m.Y");
    
    
    $user_name = get1DB("user", "id='$user_id'")['name'] . " " . get1DB("user", "id='$user_id'")['surname'];
    $teacher = get1DB("user", "id='$teacher_id'")['name'] . " " . get1DB("user", "id='$teacher_id'")['surname'];;
    $placeOfStudy = get1DB("edupage", "student_id='$user_id'")['placeOfStudy'];


    $qr = "https://heligonkovaakademia.sk/api/edupage/qr/qr.html?price=".$price."&info=".$user_name.", ".$teacher . ", " . $placeOfStudy;

    $sql = "INSERT INTO edupage_payment (user_id, lesson, date, price, status, qr) 
            VALUES ('$user_id', '$calendar_JSON', '$date', '$price', '$status', '$qr')";
    
    if (mysqli_query($conn, $sql)) return true;
    return false;
}



function inquirePayment($user_id, $month, $year, $loop = 0) {
    global $conn;

    $data = [];
    if ($loop > 1) return $data; // breaks incursion 

    $payment_id = findPayment($user_id, $month, $year);

    if ($payment_id) { // already exists
        $payment = loadPayment($payment_id);

        $data['id'] = $payment['id'];
        $data['lessonsCount'] = 0;
        if (json_decode($payment['lesson']) != null) $data['lessonsCount'] = count(json_decode($payment['lesson']));
        $data['price'] = $payment['price'];
        $data['status'] = $payment['status'];
        $data['qr'] = $payment['qr'];
        return $data;
    } 

    // does not exist, generates
    if (createPayment($user_id, $month, $year)) return inquirePayment($user_id, $month, $year, 1);
    return $data;
}



function loadPayment($id) {
    global $conn;

    $data = [];

    $sql = "SELECT * FROM edupage_payment WHERE id='$id'";
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) == 0) return $data;
    return mysqli_fetch_assoc($res);
}



function findPayment($user_id, $month, $year) {
    global $conn;

    //if (strlen($month) == 1) $month = "0" . $month;

    $sql = "SELECT id FROM edupage_payment WHERE user_id='$user_id' AND date LIKE '%$month.$year%'";
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) == 0 ) return 0;
    return mysqli_fetch_assoc($res)['id'];
}



function editPayment($post) {
    global $conn;

    $id = $post['id'];
    $user_id = $post['user_id'];
    $lesson = $post['lesson'];
    // $date = $post['date'];
    $price = $post['price'];
    $paid = $post['paid'];
    $status = $post['status'];
    $qr = $post['qr'];
    $email_payment_request = $post['email_payment_request'];
    $email_payment_notice = $post['email_payment_notice'];


    $sql = "UPDATE edupage_payment SET user_id='$user_id', lesson='$lesson', price='$price', paid='$paid',
        status='$status', qr='$qr', email_payment_request='$email_payment_request', email_payment_notice='$email_payment_notice'
        WHERE id='$id'";


    if (mysqli_query($conn, $sql)) return true;
    return false;
}



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

