<?php
header("Content-Type:application/json");


include("../../antispam.php");

if (isset($_SESSION['logged_in'])) {
    if (!empty($_GET['id']) ) {
        $id = $_GET['id'];
        $user_id = $_SESSION['id'];
        include('../../dbh.api.php');

        $sql = "SELECT id, user_id FROM product_comment WHERE id='$id'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 1) {    
            if ( $row['user_id'] == $user_id or $_SESSION['is_admin'] ) {
                $sql = "INSERT INTO product_comment (product_id, user_id, comment) VALUES ('$id', '$user_id', '$comment')";
                $sql = "DELETE FROM product_comment WHERE id='$id'";
                if (mysqli_query($conn, $sql)) response("Request succesfull");
                else response("Request failed");            
            } else response("Request failed", "Not authorised");
        } else response("Request failed", "Comment not found/Comment not owned");
    } else response("Invalid request");
} else response("Request failed", "Not logged in");





function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
