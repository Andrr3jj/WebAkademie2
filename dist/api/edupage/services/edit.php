<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function edit_all() {
    global $conn;

    $id = $_POST['id'];

    $registerer = "";
    if (!empty($_POST['registerer'])) $registerer = $_POST['registerer']; 
    $name = "";
    if (!empty($_POST['name'])) $name = $_POST['name']; 
    $surname = "";
    if (!empty($_POST['surname'])) $surname = $_POST['surname']; 
    $placeOfBirth = "";
    if (!empty($_POST['placeOfBirth'])) $placeOfBirth = $_POST['placeOfBirth']; 
    $address = "";
    if (!empty($_POST['address'])) $address = $_POST['address']; 
    $dateOfBirth = "";
    if (!empty($_POST['dateOfBirth'])) $dateOfBirth = $_POST['dateOfBirth']; 
    $experience = "";
    if (!empty($_POST['experience'])) $experience = $_POST['experience']; 
    $email = "";
    if (!empty($_POST['email'])) $email = $_POST['email']; 
    $phone = "";
    if (!empty($_POST['phone'])) $phone = $_POST['phone']; 
    $placeOfStudy = "";
    if (!empty($_POST['placeOfStudy'])) $placeOfStudy = $_POST['placeOfStudy']; 
    $formOfStudy = "";
    if (!empty($_POST['formOfStudy'])) $formOfStudy = $_POST['formOfStudy']; 
    $note = "";
    if (!empty($_POST['note'])) $note = $_POST['note']; 
    $approximateTimeOfStudy = "";
    if (!empty($_POST['approximateTimeOfStudy'])) $approximateTimeOfStudy = $_POST['approximateTimeOfStudy']; 
    $status = "";
    if (!empty($_POST['status'])) $status = $_POST['status']; 
    $teacher_id = "0";
    if (!empty($_POST['teacher_id'])) $teacher_id = $_POST['teacher_id']; 
    $parent_id = "0";
    if (!empty($_POST['parent_id'])) $parent_id = $_POST['parent_id']; 
    $student_id = "0";
    if (!empty($_POST['student_id'])) $student_id = $_POST['student_id']; 
    $timestamp_created = "0";
    if (!empty($_POST['timestamp_created'])) $timestamp_created = $_POST['timestamp_created']; 
    $timestamp_included = "0";
    if (!empty($_POST['timestamp_included'])) $timestamp_included = $_POST['timestamp_included']; 
    $timestamp_excluded = "0";
    if (!empty($_POST['timestamp_excluded'])) $timestamp_excluded = $_POST['timestamp_excluded']; 


    $sql = "UPDATE edupage SET 
        registerer='$registerer',
        name='$name',
        surname='$surname',
        placeOfBirth='$placeOfBirth',
        address='$address',
        dateOfBirth='$dateOfBirth',
        experience='$experience',
        email='$email',
        phone='$phone',
        placeOfStudy='$placeOfStudy',
        formOfStudy='$formOfStudy',
        note='$note',
        approximateTimeOfStudy='$approximateTimeOfStudy',
        status='$status',
        teacher_id='$teacher_id',
        parent_id='$parent_id',
        student_id='$student_id',
        timestamp_created='$timestamp_created',
        timestamp_included='$timestamp_included',
        timestamp_excluded='$timestamp_excluded'
        WHERE id=$id";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}

function edit_status() {
    global $conn;

    $id = $_POST['id'];
    $status = "";
    if (!empty($_GET['status'])) $status = $_GET['status'];

    $sql = "UPDATE edupage SET 
        status=$status
        WHERE id=$id";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}

function edit_teacher() {
    global $conn;

    $id = $_POST['id'];
    $teacher_id = "";
    if (!empty($_GET['teacher_id'])) $teacher_id = $_GET['teacher_id'];

    $sql = "UPDATE edupage SET 
        teacher_id=$teacher_id
        WHERE id=$id";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}
