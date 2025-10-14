<?php
header("Content-Type:application/json");

session_start();

if ($_SESSION['is_admin']) {
    if ( !empty($_GET['id']) or !empty($_GET['email']) ) {

        include('../../dbh.api.php');
        
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
            $sql = "SELECT * FROM user WHERE id='$id'";
        } else {
            $email = $_GET['email'];
            $sql = "SELECT * FROM user WHERE email='$email'";
        }
        
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) == 1) {
            while($row = mysqli_fetch_assoc($result)) {
                response("Request succesfull", $row);
            }
        } else response("Request failed", "Email not found / Multiple found"); 
    } else response("Invalid request")  ;
} else response("Request failed", "Not logged in / not admin");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
