<?php
header("Content-Type:application/json");

session_start();
include("../../antispam.php");
$logged_in = false;
if (isset($_SESSION['logged_in'])) $logged_in = true;
if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    include('../../dbh.api.php');

    if ($logged_in) {
        $email = $_SESSION['email'];
        
        $sql = "SELECT productsOwned FROM user WHERE email='$email'";
        $result = mysqli_query($conn, $sql);
    }
        
    if ($logged_in) {
        if (mysqli_num_rows($result) == 1) {
            while($row = mysqli_fetch_assoc($result)) {
                $productsOwned = $row['productsOwned'];
                if ( (!empty($productsOwned) and $productsOwned != "null") or $_SESSION['is_admin'] ) {
                    if ( (in_array($id, json_decode($productsOwned))) or $_SESSION['is_admin']) {
                        $sql = "SELECT * FROM product WHERE id='$id'";
                        $result = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($result) == 1) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $dir = $row['data'];
                            }


                            $files_data = array();
                            $files_details = array();
                            if (!empty($dir)) {
                                $filesPath_data    = '../../../data/uploads/product/' . $dir;
                                if ( file_exists( $filesPath_data ) and is_dir( $filesPath_data) ) {
                                    $files_data = scandir($filesPath_data);                                                    
                                    $files_data = array_diff(scandir($filesPath_data), array('.', '..'));
                                    $filesPath_details    = '../../../data/uploads/product/' . $dir . "/details/";
                                    if ( file_exists( $filesPath_details ) and is_dir( $filesPath_details) ) {   
                                        $files_details = scandir($filesPath_details);                                                    
                                        $files_details = array_diff(scandir($filesPath_details), array('.', '..'));
                                    }
                                }
                            }

                            if (!$logged_in) $files_data = [];

                            $response = [
                                "files_data" => $files_data,
                                "files_details" => $files_details 
                            ];
                            response("Request succesfull", $response);  
                        } else response("Request failed", "Product not found");
                    } else response("Request failed", "Product not found in owned");
                } else response("Request failed", "Product not found in owned");
        	} // spusti sa iba raz
        } else response("Request failed", "Email not found / Multiple found");
    }else {
        $sql = "SELECT * FROM product WHERE id='$id'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 1) {
            while($row = mysqli_fetch_assoc($result)) {
                $dir = $row['data'];
            }

            $files_details = array();
            if (!empty($dir)) {
                $filesPath_data    = '../../../data/uploads/product/' . $dir;
                if ( file_exists( $filesPath_data ) and is_dir( $filesPath_data) ) {
                    $filesPath_details    = '../../../data/uploads/product/' . $dir . "/details/";
                    if ( file_exists( $filesPath_details ) and is_dir( $filesPath_details) ) {   
                        $files_details = scandir($filesPath_details);                                                    
                        $files_details = array_diff(scandir($filesPath_details), array('.', '..'));
                    }
                }
            }

            if (!$logged_in) $files_data = [];

            $response = [
                "files_data" => $files_data,
                "files_details" => $files_details 
            ];
            response("Request succesfull", $response);  
        } else response("Request failed", "Product not found");
    }
} else response("Invalid request");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}
