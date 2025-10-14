<?php
header("Content-Type:application/json");

session_start();

if ($_SESSION['is_admin'] and in_array("user_master", $_SESSION['roles'])) {
    if ( !empty($_POST['id']) or !empty($_POST['email']) ) {
        
        include('../../dbh.api.php');

        if (!empty($_POST['id'])) {
            $id = $_POST['id'];
            $sql_where = "id='$id'";
        } else {
            $email = $_POST['email'];
            $sql_where = "email='$email'";
        }
        
        $ads = "";
        if (!empty($_POST['ads']))$ads = $_POST['ads'];
        $name = "";
        if (!empty($_POST['name']))$name = $_POST['name'];        
        $surname = "";
        if (!empty($_POST['surname']))$surname = $_POST['surname'];
        $phone = "";
        if (!empty($_POST['phone']))$phone = $_POST['phone'];
        $dateOfBirth = "";
        if (!empty($_POST['dateOfBirth']))$dateOfBirth = $_POST['dateOfBirth'];
        $deliveryAddressState = "";
        if (!empty($_POST['deliveryAddressState']))$deliveryAddressState = $_POST['deliveryAddressState'];
        $deliveryAddressPostcode = "";
        if (!empty($_POST['deliveryAddressPostcode']))$deliveryAddressPostcode = $_POST['deliveryAddressPostcode'];
        $deliveryAddressCity = "";
        if (!empty($_POST['deliveryAddressCity']))$deliveryAddressCity = $_POST['deliveryAddressCity'];
        $deliveryAddressStreet = "";
        if (!empty($_POST['deliveryAddressStreet']))$deliveryAddressStreet = $_POST['deliveryAddressStreet'];
        $deliveryAddressHouseNumber = "";
        if (!empty($_POST['deliveryAddressHouseNumber']))$deliveryAddressHouseNumber = $_POST['deliveryAddressHouseNumber'];
        $billingAddressState = "";
        if (!empty($_POST['billingAddressState']))$billingAddressState = $_POST['billingAddressState'];
        $billingAddressPostcode = "";
        if (!empty($_POST['billingAddressPostcode']))$billingAddressPostcode = $_POST['billingAddressPostcode'];
        $billingAddressCity = "";
        if (!empty($_POST['billingAddressCity']))$billingAddressCity = $_POST['billingAddressCity'];
        $billingAddressStreet = "";
        if (!empty($_POST['billingAddressStreet']))$billingAddressStreet = $_POST['billingAddressStreet'];
        $billingAddressHouseNumber = "";
        if (!empty($_POST['billingAddressHouseNumber']))$billingAddressHouseNumber = $_POST['billingAddressHouseNumber'];    
        
        $check = $ads.$name.$surname.$phone.$dateOfBirth.
        $deliveryAddressState.$deliveryAddressPostcode.$deliveryAddressCity.$deliveryAddressStreet.$deliveryAddressHouseNumber.
        $billingAddressState.$billingAddressPostcode.$billingAddressCity.$billingAddressStreet.$billingAddressHouseNumber;

        if ($check != "") {
            $sql = "UPDATE user SET 
                ads = '$ads',
                name = '$name',
                surname = '$surname',
                phone = '$phone',
                dateOfBirth = '$dateOfBirth',
                deliveryAddressState = '$deliveryAddressState',
                deliveryAddressPostcode = '$deliveryAddressPostcode',
                deliveryAddressCity = '$deliveryAddressCity',
                deliveryAddressStreet = '$deliveryAddressStreet',
                deliveryAddressHouseNumber = '$deliveryAddressHouseNumber',
                billingAddressState = '$billingAddressState',
                billingAddressPostcode = '$billingAddressPostcode',
                billingAddressCity = '$billingAddressCity',
                billingAddressStreet = '$billingAddressStreet',
                billingAddressHouseNumber = '$billingAddressHouseNumber'
                WHERE " . $sql_where;
            if (mysqli_query($conn, $sql)) response("Request succesfull", "Data updated");
            else response("Request failed", "Error updating record");  //mysqli_error($conn);
        } else response("Request succesfull", "Nothing changed");
    } else response("Invalid request");
} else response("Request failed", "Not logged in / not admin");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
