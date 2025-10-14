<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function register () {
    global $conn;

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
    $status = "čaká sa";
    //if (!empty($_POST['status'])) $status = $_POST['status']; 
    $timestampCreated = time();

    $sql = "INSERT INTO edupage (registerer, name, surname, placeOfBirth, address, dateOfBirth, experience, email, phone, placeOfStudy, formOfStudy, note, approximateTimeOfStudy, status, timestamp_created) 
    VALUES ('$registerer', '$name', '$surname', '$placeOfBirth', '$address', '$dateOfBirth', '$experience', '$email', '$phone', '$placeOfStudy', '$formOfStudy', '$note', '$approximateTimeOfStudy', '$status', '$timestampCreated')";
    if (mysqli_query($conn, $sql)) return true;
    return false;
}
