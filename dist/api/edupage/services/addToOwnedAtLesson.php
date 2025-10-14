<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

// include_once dirname(__DIR__) . "../../product/";


function addToOwnedAtLesson($user_id, $product_id, $lesson_id) {
    global $conn;

    $sql = "SELECT * FROM edupage_lesson WHERE id='$lesson_id'";
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $student = $row['student'];
        }
    }

    $student = json_decode($student, true);
    foreach ($student as &$s) {
        if (isset($s['student_id']) && (int)$s['student_id'] == (int)$user_id) {
            if (!isset($s['note']) || $s['note'] === null) $s['note'] = '';
            $s['note'] .= ($s['note'] === '' ? '' : ' ') . "Add notes id:" . $product_id;
        }
    }
    unset($s);

    $student = json_encode($student);
    $sql = "UPDATE edupage_lesson SET student='$student' WHERE id='$lesson_id'";
    if (mysqli_query($conn, $sql) and addToOwned($user_id, $product_id)) return true;
    return false;
}


function addToOwned($user_id, $product_id) {
    global $conn;
    $sql = "SELECT productsOwned FROM user WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        while ($row = mysqli_fetch_assoc($result)) {
            $productsOwned = $row['productsOwned'];
        }
        $productsOwned = json_decode($productsOwned);
        $product_json = array(
            'id'    =>  $product_id,
            'expires'   =>  "never"
        );
        $productsOwned[] = $product_json;            
        $productsOwned = json_encode($productsOwned);
        
        $sql = "UPDATE user SET productsOwned='$productsOwned' WHERE id='$user_id'";
        if ($conn->query($sql) === TRUE) {
          //echo "Record updated successfully";
          return true;
        }                
    }
    return false;
}
