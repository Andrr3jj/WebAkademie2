<?php
header("Content-Type:application/json");

session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in'])) {
    include('../../dbh.api.php');

    $email = $_SESSION['email'];

    $sql = "SELECT productsOwned FROM user WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) == 1) {
  		while($row = mysqli_fetch_assoc($result)) {
            response("Request succesfull", $row['productsOwned']);
  		}
    } else response("Request failed", "Email not found / Multiple found");

} else {
    response("Request failed", "Not logged in");
}



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}