<?php

function addToOwned($email, $products, $conn) {
    
    $sql = "SELECT productsOwned FROM user WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        while ($row = mysqli_fetch_assoc($result)) {
            $productsOwned = $row['productsOwned'];
        }
        $productsOwned = json_decode($productsOwned);

        if (str_contains($products[0], "subscription_")) {
            $expires = str_replace("subscription_", "", $products[0]);
            
            $product_json = array(
                'id'    =>  $products[0],
                'expires'   =>  $expires
            );
            $productsOwned[] = $product_json; $product_json;
        } else {
            if (gettype($products) == "string") $products = json_decode($products);
            foreach($products as $product) {
                $sql = "SELECT expiration FROM product WHERE id='$product'";
                $result = mysqli_query($conn, $sql);
                
                if (mysqli_num_rows($result) == 1) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $expires = $row['expiration'];
                    }
                    
                    $product_json = array(
                        'id'    =>  $product,
                        'expires'   =>  $expires
                    );
                    $productsOwned[] = $product_json;
                }
            }
            
        }

        $productsOwned = json_encode($productsOwned);
        
        $sql = "UPDATE user SET productsOwned='$productsOwned' WHERE email='$email'";
        if ($conn->query($sql) === TRUE) {
          //echo "Record updated successfully";
          return true;
        }                
        
    }
}
