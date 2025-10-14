<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";
include "./services/is_logged_in.php";

include "./services/lesson.php";
include "./services/loadStudentAdvanced.php";

if (!is_logged_in()) response("Request failed", "Not logged in");

$id = !empty($_GET['id']) ? $_GET['id'] : $_SESSION['id'];

$r = []; // response

$loadMyLessonAll = loadMyLessonAll($id);
$loadStudentsAdvanced = loadStudentsAdvanced($id);

$r['starCount'] = $loadStudentsAdvanced['starsCount'];
$r['absolvedLessonsCount'] = $loadStudentsAdvanced['absolvedLessonsCount'];
$r['canceledLessonsCount'] = $loadStudentsAdvanced['canceledLessonsCount'];
$r['canceledLessonsCountByAdmin'] = $loadStudentsAdvanced['canceledLessonsCountByAdmin'];
$r['notesAquiredCount'] = $loadStudentsAdvanced['notes_aquired_count'];
response("Reqeust succesfull", $r);
