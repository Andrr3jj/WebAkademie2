<?php
include "./services/error_reporting.php";
header("Content-Type:application/json");
session_start();
include "../dbh.api.php";
include "./services/response.php";
include "./services/is_logged_in.php";
include "./services/is_role.php";


include "./services/payment.php";

if (!is_logged_in() OR !is_role("edupageRegistrationsManager")) response("Request failed", "Not logged in");
$id = $_SESSION['id'];
if (!empty($_GET['id'])) $id = $_GET['id'];
$data = [];
for ($y = 2025; $y < 2044; $y++) {
    for ($m = 1; $m < 13; $m++) {
        $data[$y][$m] = loadPayment(findPayment($id, $m, $y));
    }

}
response("Request succesfull", $data);
