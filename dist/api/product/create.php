<?php
header("Content-Type:application/json");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include("../antispam.php");
if (isset($_SESSION['logged_in']) and $_SESSION['is_admin'] == true) {

    if (isset($_POST['name'])
    && isset($_POST['price'])
    ) {
        $roles_match = false;
        $filesPaths = array();

        $data = uniqid();
        if (!empty($_POST['data'])) $data = $_POST['data'];
        if (substr($data, -1) == "/") $data = $data . uniqid();
        
        $virtuality = true;
        if (!empty($_POST['virtuality'])) $virtuality = $_POST['virtuality'];
        if (!$virtuality) $data = 'helishop/' . $data;

        if (isset($_FILES['data_files']) && !empty($_FILES['data_files']) ) {
            $upload_destination = '../../data/uploads/product/'.$data.'/';
            if (!file_exists($upload_destination)) mkdir($upload_destination, 0777, true);
            else {
                response("Request failed", "File already exists at" . $upload_destination);
                die();
            }
            for ($i = 0; $i < count($_FILES['data_files']['tmp_name']); $i++) {
                $file = $upload_destination . basename($_FILES['data_files']['name'][$i]);
                move_uploaded_file($_FILES['data_files']['tmp_name'][$i], $file);
                $filesPaths["data"][] = $upload_destination . $_FILES['data_files']['name'][$i];
            }
            //response("Request succesfull", $filesPaths);
        }//else response("Invalid Request");


        $details = 0;
        if (!empty($_POST['details'])) $details = $_POST['details'];
/*
        if ($details) {
            $file_path = '../../data/uploads/product/'.$data.'/details/' . '.htaccess';
            $htaccess_file = fopen($file_path, "w");
            $htaccess_code = "RewriteEngine on";
            fwrite($htaccess_file, $htaccess_code);
            fclose($htaccess_file);
        }
*/
        if (isset($_FILES['details_files']) && !empty($_FILES['details_files']) ) {
            $upload_destination = '../../data/uploads/product/'.$data.'/details/';
            if (!file_exists($upload_destination)) mkdir($upload_destination, 0777, true);
            else {
                response("Request failed", "File already exists at" . $upload_destination);
                die();
            }
            for ($i = 0; $i < count($_FILES['details_files']['tmp_name']); $i++) {
                $file = $upload_destination . basename($_FILES['details_files']['name'][$i]);
                move_uploaded_file($_FILES['details_files']['tmp_name'][$i], $file);
                $filesPaths["details"][] = $upload_destination . $_FILES['details_files']['name'][$i];
            }
            //response("Request succesfull", $filesPaths);
        }//else response("Invalid Request");

        
        $virtuality = true;
        if (!empty($_POST['virtuality'])) $virtuality = $_POST['virtuality'];
        $free = false;
        if (!empty($_POST['free'])) $free = $_POST['free'];
        $new = time();
        if (!empty($_POST['new'])) $new += $_POST['new'];
        $category = "";
        if (!empty($_POST['category'])) $category = $_POST['category'];
        $name = $_POST['name'];
        $difficulty = "";
        if (!empty($_POST['difficulty'])) $difficulty = $_POST['difficulty'];
        $expiration = "never";
        if (!empty($_POST['expiration'])) $expiration = $_POST['expiration'];
        $price = $_POST['price'];
        $discount = 0;
        if (!empty($_POST['discount'])) $discount = $_POST['discount'];
        $discount_active = "false";
        if (!empty($_POST['discount_active'])) $discount_active = $_POST['discount_active'];
        $additional_text = "";
        if (!empty($_POST['additional_text'])) $additional_text = $_POST['additional_text'];

    	include('../dbh.api.php');

        $sql = "INSERT INTO product (data, details, virtuality, free, new, category, name, difficulty, expiration, price, discount, discount_active, additional_text)
        VALUES ('$data', '$details', '$virtuality', '$free', '$new', '$category', '$name', '$difficulty', '$expiration', '$price', '$discount', '$discount_active', '$additional_text')";
        
        $details = json_decode($details, true);
        $typ_ = strtolower($details['typ']);

        
        if ($typ_ == "video" and in_array("video_create", $_SESSION['roles']))$roles_match = true;
        if ($typ_ == "zapis" and in_array("numericRecord_create", $_SESSION['roles']))$roles_match = true;
        if ($typ_ == "merch" and in_array("merch_create", $_SESSION['roles']))$roles_match = true;

        if ($roles_match) {
            if (mysqli_query($conn, $sql)) {
                response("Request fullfiled", $filesPaths);
            } else {
                response("Request failed"); //mysqli_error($conn);
            }
        } else response("Request failed", "Roles mixmatch");
        

        mysqli_close($conn);
    }else{
    	response("Invalid Request");
    }

}else response("Request failed", "Not logged in");

function response($status, $data = ""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
?>