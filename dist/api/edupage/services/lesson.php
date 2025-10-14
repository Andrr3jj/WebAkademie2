<?php

include_once dirname(__DIR__) . "../../dbh.api.php";
include_once dirname(__DIR__) . "/services/loadCalendar.php";

function createLesson($month = 0, $year = 0) {
    global $conn;

    $err = false;

    //02.09.2025
    $day = 1;
    if (!$month) $month = date("m");
    if (!$year) $year = date("Y");
    $month_last_day = date("t", strtotime($day . "." . $month . "." . $year));


    while ($day <= $month_last_day) { // generovanie dna po dni
        $day_of_week = date("N", strtotime($day . "." . $month . "." . $year));
        $day_of_week -= 1;
        if ($day_of_week == 6) $day_of_week = 0;

        $sql_calendar = "SELECT * FROM edupage_calendar WHERE availability_day='$day_of_week'";
        $res_calendar = mysqli_query($conn, $sql_calendar);
        if (!$res_calendar) continue;
        while ($calendar_row = mysqli_fetch_assoc($res_calendar)) {
            $calendar_id = $calendar_row['id'];
            
            $day_2d = $day;
            if (strlen($day_2d) == 1 ) $day_2d = "0" . $day_2d;
            $date = $day_2d . "." . $month . "." . $year;



            if (findLesson($calendar_id, $date)) {
                $lesson = loadLesson(0, $calendar_id, $date)[0];
                if ($lesson['status'] != "planned") continue; // prevents regenerating already canceled/edited lessons
                if (!str_contains($lesson['student'], '"stars":0,"note":0,"attendance_status":0,"played":0}]')) continue;
                
                $lesson_id = getLessonIdByCalendarId($calendar_id, $date);
                if ($lesson_id) deleteLesson($lesson_id);
            }


            $teacher_id = $calendar_row['teacher_id'];
            $student = [];
            if (!empty($calendar_row['student'])) {
                $calendar_row['student'] = json_decode($calendar_row['student']);
                foreach ($calendar_row['student'] as $s) {
                    $student_data = [
                        'student_id' => $s,
                        'stars' => 0,
                        'note' => 0,
                        'attendance_status' => 0,
                        'played' => 0
                    ];
                    $student[] = $student_data;
                }
            }
            $student = json_encode($student);
            
            $hour = $calendar_row['availability_hour'];
            $status = "planned";
        

            $sql = "INSERT INTO edupage_lesson (calendar_id, teacher_id, student, date, hour, status) 
                VALUES ('$calendar_id', '$teacher_id', '$student', '$date', '$hour', '$status')";
            if (!findLesson($calendar_id, $date)) {
                if (!mysqli_query($conn, $sql)) $err = true;
            }


        }
        $day++;
    }
    if ($err) return false;
    return true;
}


function findLesson($calendar_id, $date) {
    global $conn;

    $sql = "SELECT * FROM edupage_lesson WHERE calendar_id='$calendar_id' AND date='$date'";
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) == 0) return false;
    return true;
}

function loadLesson($id = 0, $calendar_id = 0, $date = '') {
    global $conn;

    $data = [];

    $sql = "SELECT * FROM edupage_lesson";
    if ($id or $calendar_id or $date) $sql .= " WHERE ";

    if ($id) $sql .= " id='$id'";
    if ($calendar_id) {
        if (str_contains($sql, "=")) $sql .= " AND ";
        $sql .= " calendar_id='$calendar_id'";
    } 
    if ($date !== '') {
        $safeDate = mysqli_real_escape_string($conn, $date);
        if (str_contains($sql, "=")) $sql .= " AND ";
        $sql .= " date='$safeDate'";
    }
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    return $data;
}



function editLesson($id) {
    global $conn;

    $calendar_id = "";
    if (!empty($_POST['calendar_id'])) $calendar_id = (int)$_POST['calendar_id'];
    $teacher_id = "";
    if (!empty($_POST['teacher_id'])) $teacher_id = (int)$_POST['teacher_id'];
    $student = "";
    if (!empty($_POST['student'])) $student = mysqli_real_escape_string($conn, $_POST['student']);
    $date = "";
    if (!empty($_POST['date'])) $date = mysqli_real_escape_string($conn, $_POST['date']);
    $hour = "";
    if (!empty($_POST['hour'])) $hour = mysqli_real_escape_string($conn, $_POST['hour']);
    $status = "";
    if (!empty($_POST['status'])) $status = mysqli_real_escape_string($conn, $_POST['status']);

    $sql = "UPDATE edupage_lesson SET 
        calendar_id='$calendar_id', 
        teacher_id='$teacher_id', 
        student='$student', 
        date='$date', 
        hour='$hour', 
        status='$status' 
        WHERE id='$id'";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}



function getLessonIdByCalendarId($id, $date = 0) {
    global $conn;
    $sql = "SELECT id FROM edupage_lesson WHERE calendar_id='$id'";
    if ($date) $sql .= " AND date='$date'";
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) > 0) while($row = mysqli_fetch_assoc($res)) return $row['id'];
    return 0;
}


function deleteLesson($id) {
    global $conn;
    $sql = "DELETE FROM edupage_lesson WHERE id='$id'";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}



function existsLesson($id) {
    global $conn;
    $sql = "SELECT id FROM edupage_lesson WHERE id='$id'";
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) > 0) return true;
    return false;
}



function loadMyLessonAll($student_id) {
    global $conn;
    
    $data = [];

    $search_0 = '"' . $student_id . '"';
    $search_1 = ":" . $student_id . ",";
    $sql = "SELECT * FROM edupage_lesson WHERE student LIKE '%$search_0%' OR student LIKE '%$search_1%'";
    $res = mysqli_query($conn, $sql);

    if (mysqli_num_rows($res) == 0) return $data;
    
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }
    return $data;
}

function cancelLesson($lesson_id) {
    global $conn;
 
    $lesson = loadLesson($lesson_id);
    $lesson_student = json_decode($lesson['student'], true);
    //$lesson_student[0]['attendance_status'] = "true";
    //if (count($lesson_student) > 1) $lesson_student[1]['attendance_status'] = "true";
    $lesson_student = json_encode($lesson_student);

    $sql = "UPDATE edupage_lesson SET status='canceled_parent', student='$lesson_student' WHERE id='$lesson_id'";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}


function deleteDuplicate() {
    global $conn;

    $lessons_all = [];

    $sql = "SELECT * FROM edupage_lesson";
    $res = mysqli_query($conn, $sql);
    if (!$res) return true;
    while ($lessons_all[] = mysqli_fetch_assoc($res));

    foreach ($lessons_all as $lesson_compare) {
        $lesson_id = $lesson_compare['id'];
        $calendar_id = $lesson_compare['calendar_id'];
        $date = $lesson_compare['date'];
        
        $sql = "SELECT * FROM edupage_lesson WHERE calendar_id='$calendar_id' AND date='$date' AND id!='$lesson_id'";
        $res = mysqli_query($conn, $sql);
        if (!$res) continue;
        while ($lesson = mysqli_fetch_assoc($res)) {
            if ($lesson['status'] != "planned") continue; // prevents regenerating already canceled/edited lessons
            if (!str_contains($lesson['student'], '"stars":0,"note":0,"attendance_status":0,"played":0}]')) continue;
            $lesson_id = getLessonIdByCalendarId($calendar_id, $date);
            if ($lesson_id) deleteLesson($lesson_id);
        }
    }
}
