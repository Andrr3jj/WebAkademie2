<?php

include_once dirname(__DIR__) . "../../dbh.api.php";
include_once dirname(__DIR__) . "../../achievements/services/progress_load.php";

function loadStudentsAdvanced($id) {
    global $conn;

    $data = [];

    $sql = "SELECT * FROM user WHERE id='$id' ";
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $data['name'] = $row['name'];
            $data['surname'] = $row['surname'];
            $data['email'] = $row['email'];
            $data['profilePhotoUrl'] = $row['profilePhotoUrl'];
            $data['helitime'] = $row['helitime'];
            $data['online'] = $row['online'];
        }
    }


    
    $progress = progress_load($id);
    $finished = 0;
    foreach ($progress as $p) {
        if ($p['finished_date'] != "") $finished += 1; 
    }
    $data['achievementsFulfiledCount'] = $finished;


    $data['allLessonsCount'] = 0;
    $data['absolvedLessonsCount'] = 0;
    $data['canceledLessonsCount'] = 0;
    $data['canceledLessonsCountByAdmin'] = 0;
    $data['starsCount'] = 0;
    $data['lastHourStar'] = 0;
    $data['notes_aquired_count'] = 0;
    $data['note'] = "";


    $search = ':'.$id.',';
    $search_1 = '"'.$id.'"';
    $sql = "SELECT * FROM edupage_lesson WHERE student LIKE '%$search%' OR student LIKE '%$search_1%' ORDER BY id ASC";
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $student = json_decode($row['student'], true);
            if ($row['status'] == "planned") continue;
            foreach ($student as $s) {
                if ($s['student_id'] == $id) {
                    $data['allLessonsCount'] += 1;
                    if ($s['attendance_status'] === false) {
                        $data['absolvedLessonsCount'] += 1;
                    } elseif ($s['attendance_status'] === true) {
                        $data['canceledLessonsCount'] += 1;
                    } elseif ($s['attendance_status'] === null) {
                        $data['canceledLessonsCountByAdmin'] += 1;
                    }
                    $data['starsCount'] += $s['stars'];
                    if (str_contains($data['note'], "Add notes ") )$data['notes_aquired_count'] = count(explode("Add notes ", $data['note']));
                    $data['note'] .= $s['note'];
                    $data['lastHourStar'] = $s['stars'];
                }
            }
        }
    }




    $data['edupage_registration_student'] = [];
    $data['edupage_registration_parent'] = [];

    $sql = "SELECT id, email, phone, student_id, parent_id FROM edupage WHERE student_id='$id' OR parent_id='$id'";
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            if ($row['student_id'] == $id) $data['edupage_registration_student'][$row['id']] = $row;
            else $data['edupage_registration_parent'][$row['id']] = $row;
        }
    }

    return $data;
}
