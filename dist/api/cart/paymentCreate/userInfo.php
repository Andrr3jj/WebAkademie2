<?php

$sql = "SELECT * FROM user WHERE id='$user_id'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 1) {
    while($row = mysqli_fetch_assoc($result)) {
        $first_name = $row['name'];
        $last_name = $row['surname'];
        $email = $row['email'];
        $city = $row['billingAddressCity'];
        $street = $row['billingAddressStreet'] . " " . $row['billingAddressHouseNumber'];
        $postal_code = $row['billingAddressPostcode'];
        $state = $row['billingAddressState'];
        $billingAddress = $row['billingAddressState'] . " " .$postal_code . " " . $city . " " . $street;
        $deliveryAddress = $row['deliveryAddressState'] . " " . $row['deliveryAddressPostcode'] . " " . $row['deliveryAddressCity'] . " " . $row['deliveryAddressStreet'] . " " . $row['deliveryAddressHouseNumber'];

    }
}
