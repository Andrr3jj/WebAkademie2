<?php
header("Content-Type:application/json");

session_start();
include("../../antispam.php");
if (isset($_SESSION['logged_in'])) {
    include('../../dbh.api.php');

    $id = $_SESSION['id'];
    $email = $_SESSION['email'];

    $data['edupage_in_calendar'] = false;
    $search = '"'.$id.'"';
    $sql = "SELECT * FROM edupage_calendar WHERE student LIKE '%$search%'";
    $result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) $data['edupage_in_calendar'] = true;




    $sql = "SELECT * FROM user WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) == 1) {
  		while($row = mysqli_fetch_assoc($result)) {
            $data['id'] = $row['id'];
            $data['email'] = $row['email'];
            $data['phone'] = $row['phone'];
            $data['name'] = $row['name'];
            $data['surname'] = $row['surname'];
            $data['dateOfBirth'] = $row['dateOfBirth'];
            $data['deliveryAddressState'] = $row['deliveryAddressState'];
            $data['deliveryAddressPostcode'] = $row['deliveryAddressPostcode'];
            $data['deliveryAddressCity'] = $row['deliveryAddressCity'];
            $data['deliveryAddressStreet'] = $row['deliveryAddressStreet'];
            $data['deliveryAddressHouseNumber'] = $row['deliveryAddressHouseNumber'];
            $data['billingAddressState'] = $row['billingAddressState'];
            $data['billingAddressPostcode'] = $row['billingAddressPostcode'];
            $data['billingAddressCity'] = $row['billingAddressCity'];
            $data['billingAddressStreet'] = $row['billingAddressStreet'];
            $data['billingAddressHouseNumber'] = $row['billingAddressHouseNumber'];
            $data['profilePhotoUrl'] = $row['profilePhotoUrl'];
            $data['ads'] = $row['ads'];
            $data['accesses'] = $row['accesses'];
            $data['credit'] = $row['credit'];


            $data['edupage_registration_student'] = [];
            $data['edupage_registration_parent'] = [];

            $sql_reg = "SELECT id, email, phone, student_id, parent_id FROM edupage WHERE student_id='$id' OR parent_id='$id'";
            $res_reg = mysqli_query($conn, $sql_reg);
            if (mysqli_num_rows($res_reg) > 0) {
                while ($row_reg = mysqli_fetch_assoc($res_reg)) {
                    if ($row_reg['student_id'] == $id) $data['edupage_registration_student'][$row_reg['id']] = $row_reg;
                    else $data['edupage_registration_parent'][$row_reg['id']] = $row_reg;
                }
            }

            $data['edupage_in_calendar'] = false;
            $search_in_calendar = '"'.$id.'"';
            $sql_in_calendar = "SELECT * FROM edupage_calendar WHERE student LIKE '%$search_in_calendar%'";
            $result_in_calendar = mysqli_query($conn, $sql_in_calendar);
	        if (mysqli_num_rows($result_in_calendar) > 0) $data['edupage_in_calendar'] = true;

            response("Request succesfull", $data);
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