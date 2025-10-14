<?php
header("Content-Type:application/json");

session_start();

if (isset($_SESSION['logged_in'])) {
	$id = $_SESSION['id'];

	include('../../dbh.api.php');

    $sql = "SELECT is_admin, roles FROM user WHERE id='$id'";
    $result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) > 0) {
  		while($row = mysqli_fetch_assoc($result)) {
			$_SESSION['is_admin'] = $row['is_admin'];
			$_SESSION['roles'] = $row['roles'];
			//print_r($_SESSION['roles']);
			//if (!empty($row['roles'])) $_SESSION['roles'] = 
			//$_SESSION['roles'] = array(eval($row['roles']));
			//eval("\$_SESSION['roles'] = array( \"$row['roles']\");");
			if (!empty($row['roles'])) $_SESSION['roles'] = explode(",",str_replace('"', '', str_replace("'","",str_replace("]","",str_replace("[","",str_replace(" ", "", $row['roles']))))));
			//echo "00";
			//print_r($_SESSION['roles']);
			

			//if (in_array("subscription_edit", $_SESSION['roles'])) echo "w";
			response("Request fullfiled", $row['roles']);
  		}
	} else response("Request failed", "Not found");
}else response("Invalid Request");


function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
