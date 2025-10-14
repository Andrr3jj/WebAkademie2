<?php
header("Content-Type:application/json");


include("../../antispam.php");

if (isset($_SESSION['logged_in'])) {
    if (!empty($_POST['id']) and !empty($_POST['comment']) ) {
        $id = $_POST['id'];
        $user_id = $_SESSION['id'];
        $comment = $_POST['comment'];
        include('../../dbh.api.php');

        $sql = "SELECT id FROM product WHERE id='$id'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 1) {    
            $sql = "INSERT INTO product_comment (product_id, user_id, comment) VALUES ('$id', '$user_id', '$comment')";
            if (mysqli_query($conn, $sql)) response("Request succesfull");
            else response("Request failed");            
        } else response("Request failed", "Product not found");
    } else response("Invalid request");
} else response("Request failed", "Not logged in");





function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
