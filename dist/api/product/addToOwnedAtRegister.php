<?php

function addToOwnedAtRegister($email, $conn) {
    
    $sql = "SELECT productsOwned FROM user WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        while ($row = mysqli_fetch_assoc($result)) {
            $productsOwned = $row['productsOwned'];
        }
        if ( (empty($productsOwned) || $productsOwned == "null" || $productsOwned == "{}" || $productsOwned == "[]") ) {
            $productsOwned = json_decode($productsOwned);
            
            $load_products_free = array();
            $sql_free = "SELECT id FROM product where deleted='false' and virtuality='true' and free='true'";
            $result_free = mysqli_query($conn, $sql_free);
            if (mysqli_num_rows($result_free) > 0) {
                while($row = mysqli_fetch_assoc($result_free)) {
                    array_push($load_products_free, $row);
                }
                $picked = array_rand($load_products_free);
                $picked = $load_products_free[$picked];
                $picked_array = array(
                    'id'    =>  $picked['id'],
                    'expires'   =>  "never"
                );
                $productsOwned_new = array();
                $productsOwned_new[] = $picked_array;


                $productsOwned_new = json_encode($productsOwned_new);
                
                $sql = "UPDATE user SET productsOwned='$productsOwned_new' WHERE email='$email'";
                if ($conn->query($sql) === TRUE) {
                  //echo "Record updated successfully";
                }                
            } 
        } 
    }
}
