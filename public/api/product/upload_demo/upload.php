<?php

if (isset($_FILES['data']) && !empty($_FILES['data'])) {
    $upload_destination = 'uploads/';
    for ($i = 0; $i < count($_FILES['data']['tmp_name']); $i++) {
        $file = $upload_destination . $_FILES['data']['name'][$i];
        move_uploaded_file($_FILES['data']['tmp_name'][$i], $file);
    }
    echo 'Nahravanie dokončené!';
} else echo 'Nahravanie zlyhalo';

?>