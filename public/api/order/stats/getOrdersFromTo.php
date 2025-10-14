<?php
header("Content-Type:application/json");

session_start();

if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true) {

    if (isset($_POST['from']) && isset($_POST['to']) ) {
        $from = $_POST['from'];
        $to = $_POST['to'];

        if ($from > $to) $to = $from + 86400;   // +1 day

    	include('../../dbh.api.php');


        $sql = "SELECT * FROM `order` WHERE `timestamp` > $from AND `timestamp` < $to";
        $result = mysqli_query($conn, $sql);

        $data = array();
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        
        response("Request succesfull", $data);

        mysqli_close($conn);
    }else response("Invalid Request");
}else response("Request failed", "Not logged in");

function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
