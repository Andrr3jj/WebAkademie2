<?php

include_once dirname(__DIR__) . "../../dbh.api.php";

function listStars() {
    global $conn;

    $data = [];


    $sql_user = "SELECT id, helitime, name, surname, profilePhotoUrl, online FROM `user` WHERE is_admin='0' ORDER BY helitime DESC";
    $result_user = mysqli_query($conn, $sql_user);
    if (mysqli_num_rows($result_user) > 0) {
        $count = 1;
        while($row = mysqli_fetch_assoc($result_user)) {
            $user_id = $row['id'];



            $user['id'] = $row['id'];
            $user['rank'] = $count;
            $user['name'] = $row['name'];
            $user['surname'] = $row['surname'];
            $user['profilePhotoUrl'] = $row['profilePhotoUrl'];
            $user['helitime'] = $row['helitime'];
            $user['online'] = $row['online'];

            $stars = 0;
            $sql_stars = "SELECT student FROM edupage_lesson WHERE student LIKE '%:$user_id,%'";
            $res_stars = mysqli_query($conn, $sql_stars);
            if (mysqli_num_rows($res_stars) > 0) {  
                while ($row_stars = mysqli_fetch_assoc($res_stars)) {
                    $student = json_decode($row_stars['student'], true);
                    foreach ($student as $s) {
                        if ($s['student_id'] == $user_id) $stars += $s['stars'];
                    }
                }
            } 
            $user['stars'] = $stars;


            $data[$count] = $user;

            $count += 1; 
        }
    }

    $stars = array_column($data, 'stars');
    array_multisort($stars, SORT_DESC, $data);

    $data_old = $data;
    $data = [];

    for($i = 0; $i < 30; $i++) $data[] = $data_old[$i];

    return $data;
}
