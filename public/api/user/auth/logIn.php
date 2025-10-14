<?php
header("Content-Type:application/json");

ini_set('session.gc_maxlifetime', 2629743); // 2629743s is about one month
session_set_cookie_params(2629743);

session_start();
include("../../antispam.php");
include('../../inputVerification.php');
if (isset($_POST['email']) && verifyEmail($_POST['email'])
&& isset($_POST['password']) && verifyName($_POST['password'])
) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

	include('../../dbh.api.php');

    $sql = "SELECT id, email, password, is_admin, roles FROM user WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) > 0) {
  		while($row = mysqli_fetch_assoc($result)) {
    		if ($row['password'] == $password) {
				if (!isset($_SESSION['logged_in'])) {
					$_SESSION['id'] = $row['id'];
					$_SESSION['logged_in'] = true;
					$_SESSION['email'] = $row['email'];
					$_SESSION['is_admin'] = $row['is_admin'];
					$_SESSION['roles'] = $row['roles'];
					if (!empty($row['roles'])) $_SESSION['roles'] = explode(",",str_replace('"', '', str_replace("'","",str_replace("]","",str_replace("[","",str_replace(" ", "", $row['roles']))))));
					response("Request fullfiled");
				} else response("Request failed", "Already logged in");
			} else {
				response("Request failed", "Wrong password");
			}
  		}
	} else {
		response("Request failed", "Email not found");
	}


    mysqli_close($conn);
}else{
	response("Invalid Request");
}

function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
