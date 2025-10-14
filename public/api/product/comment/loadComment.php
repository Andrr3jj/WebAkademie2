<?php
header("Content-Type:application/json");


include("../../antispam.php");

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    include('../../dbh.api.php');
    
    $sql = "SELECT id, comment, user_id FROM product_comment WHERE product_id='$id'";
    $result = mysqli_query($conn, $sql);
    $number_of_results = mysqli_num_rows($result);
    if ($number_of_results > 0) {
        $comments = array();
        while($row = mysqli_fetch_assoc($result)) {
            $comment = [
                'id' => $row['$id'],
                'comment' => $row['comment'],
                'owned' => $row['user_id'] = $_SESSION['id'] ? true : false
            ];
            $comments = array_push($comments, $comment);
        }
        response("Request succesfull", $comments);
    } else response("Request failed", "Product not found / No comments yet");
} else response("Invalid request");

    




function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
