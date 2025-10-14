<?php
header("Content-Type:application/json");

session_start();
include("../antispam.php");
if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    include('../dbh.api.php');


    $sql = "SELECT
        id,
        details,
        deleted,
        virtuality,
        free,
        new,
        category,
        name,
        difficulty,
        expiration,
        price,
        discount,
        discount_active,
        additional_text,
        data
     FROM product WHERE id='$id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            //if ($row['deleted'] == "true") response("Request falied", "Product deleted");  
            //else response("Request succesfull", $row);  
            if ($row['virtuality'] == "false") {

                $path = '../../data/uploads/product/' . $row['data'] . "/";
                $images = scandir($path);
                //$images = array_diff($images, array('.', '..'));
                $images_url = [];
                if ($images) {
                    $path = 'https://heligonkovaakademia.sk/data/uploads/product/' . $row['data'] . "/";
                    foreach ($images as $img) {
                        if (!in_array($img, ['.', '..'])){
                            $images_url[] = $path . $img;
                        }
                    }
                }
                
                $row['images'] = $images_url;
            }   else unset($row['data']);  
            response("Request succesfull", $row);  
        }
    } else response("Request failed", "Product not found");
} else response("Invalid request");



function response($status, $data=""){
    $response['status'] = $status;
    $response['data'] = $data;
	
	$json_response = json_encode($response);
	echo $json_response;
}