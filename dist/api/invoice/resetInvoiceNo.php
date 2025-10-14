<?php
if (!empty($_GET['pass']) and $_GET['pass'] == "cron") {
    $myfile = fopen("../invoice/invoice_no.txt", "w+");
    $invoice_no = 0;
    fwrite($myfile, $invoice_no);
    fclose($myfile);
}