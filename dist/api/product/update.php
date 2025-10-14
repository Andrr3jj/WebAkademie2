<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");
if (isset($_SESSION['logged_in']) and !empty($_SESSION['is_admin']) and $_SESSION['is_admin'] == true) {
    if (!empty($_POST['id'])) {
        $roles_match = false;
        //if (!empty($_POST['id'])) $id = $_POST['id'];
        //if (!empty($_GET['id'])) $id = $_GET['id'];
        $id = $_POST['id'];

        include('../dbh.api.php');

        $data = "";
        if (!empty($_POST['data']))$data = $_POST['data'];
        if (isset($_FILES['data_files']) && !empty($_FILES['data_files']) ) {
            $upload_destination = '../../data/uploads/product/'.$data.'/';
            
            $files = glob($upload_destination . "*");
            foreach($files as $file){ 
              if(is_file($file)) {
                unlink($file); 
              }
            }

            for ($i = 0; $i < count($_FILES['data_files']['tmp_name']); $i++) {
                $file = $upload_destination . $_FILES['data_files']['name'][$i];
                move_uploaded_file($_FILES['data_files']['tmp_name'][$i], $file);
                $filesPaths["data"][] = $upload_destination . $_FILES['data_files']['name'][$i];
            }
            //response("Request succesfull", $filesPaths);
        }//else response("Invalid Request");

        $details = "";
        if (!empty($_POST['details']))$details = $_POST['details'];
        if (isset($_FILES['details_files']) && !empty($_FILES['details_files']) ) {
            $upload_destination = '../../data/uploads/product/'.$data.'/details/';

            $files = glob($upload_destination . "*");
            foreach($files as $file){ 
              if(is_file($file)) {
                unlink($file); 
              }
            }

            for ($i = 0; $i < count($_FILES['details_files']['tmp_name']); $i++) {
                $file = $upload_destination . $_FILES['details_files']['name'][$i];
                move_uploaded_file($_FILES['details_files']['tmp_name'][$i], $file);
                $filesPaths["details"][] = $upload_destination . $_FILES['details_files']['name'][$i];
            }
            //response("Request succesfull", $filesPaths);
        }//else response("Invalid Request");

        $deleted = "";
        if (!empty($_POST['deleted']))$deleted = $_POST['deleted'];
        $virtuality = "";
        if (!empty($_POST['virtuality']))$virtuality = $_POST['virtuality'];
        $free = "";
        if (!empty($_POST['free']))$free = $_POST['free'];
        $new = "";
        if (!empty($_POST['new']))$new = $_POST['new'];
        $category = "";
        if (!empty($_POST['category']))$category = $_POST['category'];
        $name = "";
        if (!empty($_POST['name']))$name = $_POST['name'];
        $difficulty = "";
        if (!empty($_POST['difficulty']))$difficulty = $_POST['difficulty'];
        $expiration = "";
        if (!empty($_POST['expiration']))$expiration = $_POST['expiration'];
        $price = "";
        if (!empty($_POST['price']))$price = $_POST['price'];
        $discount = 0;
        if (!empty($_POST['discount'])) $discount = $_POST['discount'];
        $discount_active = "false";
        if (!empty($_POST['discount_active'])) $discount_active = $_POST['discount_active'];
        $additional_text = "";
        if (!empty($_POST['additional_text'])) $additional_text = $_POST['additional_text'];
        
        $check = $data.$details.$deleted.$virtuality.$free.$new.$category.$name.$difficulty.$expiration.$price;

        if ($check != "") {
            $sql = "UPDATE product SET 
                data = '$data',
                details = '$details',
                deleted = '$deleted',
                virtuality = '$virtuality',
                free = '$free',
                new = '$new',
                category = '$category',
                name = '$name',
                difficulty = '$difficulty',
                expiration = '$expiration',
                price = '$price',
                discount = '$discount',
                discount_active = '$discount_active',
                additional_text = '$additional_text'
                WHERE id='$id'";


            if ($id < 4 and in_array("subscription_edit", $_SESSION['roles'])) $roles_match = true;
            else {
                $details = json_decode($details, true);

                $typ_ = strtolower($details['typ']);
                  
                if ($typ_ == "video" and in_array("video_edit", $_SESSION['roles']))$roles_match = true;
                if ($typ_ == "zapis" and in_array("numericRecord_edit", $_SESSION['roles']))$roles_match = true;
                if ($typ_ == "merch" and in_array("merch_edit", $_SESSION['roles']))$roles_match = true;
            } 

            if ($roles_match) {
                if (mysqli_query($conn, $sql)) response("Request fullfiled", "Data updated");
                else response("Request failed", "Error updating record");  //mysqli_error($conn);
            } else response("Request failed", "Roles mixmatch");
        } else response("Invalid request");
    } else response("Invalid request");
}else response("Request failed", "Not logged in");


function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}