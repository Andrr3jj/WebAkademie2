<?php
/*
if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
    die();
    //die ("<h2>Access Denied!</h2>");
}
*/
// cena sa udáva v centoch!
// na testovanie prostredníctvom gopay musí cena končiť xyz.00 alebo xyz.04


$sql = "SELECT id, price, details FROM product WHERE id < 4";
$result = mysqli_query($conn, $sql);

$data = array();
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}


$subscription_available = [
    "mesiac" => [
        "duration" => "2592000",
        "price" => intval( json_decode($data[0]['details'], true)['aktivnaZlava'] ? (100 - json_decode($data[0]['details'], true)['zlava']) / 100 * ($data[0]['price'] * 100) : $data[0]['price'] * 100 )
    ],
    "pol rok" => [
        "duration" => "15778463",
        "price" => intval( json_decode($data[1]['details'], true)['aktivnaZlava'] ? (100 - json_decode($data[1]['details'], true)['zlava']) / 100 * ($data[1]['price'] * 100) : $data[1]['price'] * 100 )
    ],
    "rok" => [
        "duration" => "31556926",
        "price" => intval( json_decode($data[2]['details'], true)['aktivnaZlava'] ? (100 - json_decode($data[2]['details'], true)['zlava']) / 100 * ($data[2]['price'] * 100) : $data[2]['price'] * 100 )
    ]
];
