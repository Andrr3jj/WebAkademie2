<?php

if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
    die();
    //die ("<h2>Access Denied!</h2>");
}

$mail_config = [
    "Host" => "smtp.websupport.sk",
    "SMTPAuth" => true,
    "Username" => "info@heligonkovaakademia.sk",
    "Password" => "Xo0f]3U9Yj",
    "setFrom" => [
        'info@heligonkovaakademia.sk', 
        'Heligónková Akadémia'
    ],
    "addReplyTo" => [
        'objednavky@heligonkovaakademia.sk',
        'Heligónková Akadémia'
    ],

    "subject_ads" => "Heligónková Akadémia - Reklamný email",
    "subject_forgotPassword" => "Heligónková Akadémia - Zabudnuté heslo",
    "subject_paymentSuccesfull" => "Heligónková Akadémia - Objednávka úspešne dokončená",
    "subject_paymentUnsucessfull" => "Heligónková Akadémia - Problém s platbou",
    "subject_register" => "Heligónková Akadémia - Úspešná registrácia",
    "subject_edupageCalendar" => "Boli ti pridelené tieto hodiny",
    "subject_edupageCalendarEmail" => "Zaradenie do rozvrhu – výučba heligónky potvrdená",
    "subject_edupageRegister" => "Ďakujeme za vyplnenie prihlášky!",
    "subject_edupageCalendarUnregisterSendEmail" => "Zrušenie lekcií",
    "subject_edupagePaymentRequest" => "Príkaz k úhrade za výučbu",
    "subject_edupagePaymentNotice" => "Upozornenie na neuhradenú platbu",
    
];        
