<?php
require __DIR__ . '/vendor/autoload.php';

use HeadlessChromium\BrowserFactory;

//$browserFactory = new BrowserFactory();
$browserFactory = new BrowserFactory('google-chrome');
//$browserFactory = new BrowserFactory('google-chrome');
//$browserFactory = new BrowserFactory('chrome');
//$browserFactory = new BrowserFactory('chromium-browser');

$id = 0;
if (!empty($_POST['id'])) $id = $_POST['id'];
$input = $_POST['input'];

// starts headless Chrome
$browser = $browserFactory->createBrowser();

try {
    // creates a new page and navigate to an URL
    $page = $browser->createPage();
    $page->setHtml($input, 240000); // html
    //$page->navigate(input)->waitForNavigation(120000); // url


    //$page->pdf(['printBackground' => true])->saveToFile($id.'.pdf', 239000);
    
    $margin = 0.0;
    $options = [
        'printBackground'     => true,             // default to false
        //'displayHeaderFooter' => true,             // default to false
        //'preferCSSPageSize'   => true,             // default to false (reads parameters directly from @page)
        'marginTop'           => $margin,              // defaults to ~0.4 (must be a float, value in inches)
        'marginBottom'        => $margin,              // defaults to ~0.4 (must be a float, value in inches)
        'marginLeft'          => $margin,              // defaults to ~0.4 (must be a float, value in inches)
        'marginRight'         => $margin,              // defaults to ~0.4 (must be a float, value in inches)
        //'footerTemplate'      => '<div class="pageNumber">cislo</div>', // see details above
    ];
    
    $pdf = $page->pdf($options);
    $pdf->saveToFile($id.'.pdf', 239000);
} finally {
    $browser->close();
}