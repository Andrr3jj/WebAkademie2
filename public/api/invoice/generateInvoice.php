<?php


function dot2comma($string) {
    return str_replace(".", ",", $string);
}

function generateInvoice($inputData) {

    require(__dir__ . "/tfpdf.php");

    class PDF extends tFPDF {
        function body($data) {
            global $fontName;

            $fontName = 'DejaVu';
            $this->AddFont('DejaVu', '', 'DejaVuSansCondensed.ttf', true);
            $this->AddFont('DejaVu', 'B', 'DejaVuSansCondensed-Bold.ttf', true);


            $this->SetY(5);
            $this->SetX(-50);
            $this->SetFont($fontName, 'B', 12);
            $this->Cell(41, 10, "FAKTÚRA " . $data['invoice_no'], 0, 1, "R");

            $this->SetDrawColor(142, 200, 78);
            $this->SetFillColor(142, 200, 78);
            $this->Rect(10, 13, 190, 3, "F");



            $this->SetY(20);
            $this->SetFont($fontName, 'B', 12);
            $this->Cell(50, 10, "DODÁVATEĽ", 0, 1);
            $this->SetFont($fontName, '', 12);
            $this->Cell(50, 7, $data['shop_name'], 0, 1);
            $this->Cell(50, 7, $data['shop_address'], 0, 1);
            $this->Cell(50, 7, "IČO: " . $data['shop_ico'] . "  DIČ: " . $data['shop_dic'], 0, 1);
            $this->Cell(50, 7, "Nie je platiteľ DPH", 0, 1);
            $this->SetY($this->getY() + 3);
            $this->SetFont($fontName, '', 8);
            $this->Cell(50, 5, "Obchodný register Okresného súdu Žilina,", 0, 1);
            $this->Cell(50, 5, "oddiel: Sro, vložka č. 79749/L", 0, 1);


            $this->SetY(20);
            $this->SetX(120);
            $this->SetFont($fontName, 'B', 12);
            $this->Cell(50, 10, "ODBERATEĽ: ", 0, 1);
            $this->SetX(120);
            $this->SetFont($fontName, '', 12);
            $this->Cell(50, 7, $data["customer_name"], 0, 1);
            if (is_array($data['customer_address'])) {
                foreach ($data['customer_address'] as $customer_address) {
                    $this->SetX(120);
                    $this->Cell(50, 7, $customer_address, 0, 1);
                }
            } else {
                $this->SetX(120);
                $this->Cell(50, 7, $data['customer_address'], 0, 1);
            }



            $this_sectionY = $this->GetY() + 30;
            $this->SetY($this_sectionY);

            $this->SetX(10);
            $this->Cell(50, 5, "Dátum vystavenia: ", 0, 0);
            $this->Cell(50, 5, $data["invoice_date"], 0, 1);
            $this->Cell(50, 5, "Dátum dodania: ", 0, 0);
            $this->Cell(50, 5, $data["invoice_date"], 0, 1);
            $this->Cell(50, 5, "Splatnosť: ", 0, 0);
            $this->Cell(50, 5, $data["invoice_date"], 0, 1);

            $this->SetTextColor(255, 255, 255);
            $this->Rect(90, $this_sectionY, 69, 16, "F"); #1
            $this->SetFont($fontName, "", 9);
            $this->Text(93, $this_sectionY + 5, "Suma:");
            $this->SetFont($fontName, "B", 14);
            $this->Text(93, $this_sectionY + 11, dot2comma($data['price_total']) . " EUR");
            $this->Rect(90, $this_sectionY + 17, 69, 24, "F"); #2
            $this->SetFont($fontName, "", 9);
            $this->Text(93, $this_sectionY + 24, "Spôsob úhrady:");
            $this->Text(93, $this_sectionY + 28, "Variabilný symbol:");
            $this->SetFont($fontName, "B", 9);
            $this->Text(121, $this_sectionY + 28, $data['invoice_no']);
            $this->SetFont($fontName, "", 9);
            $this->Text(93, $this_sectionY + 32, "IBAN:");
            $this->SetFont($fontName, "B", 9);
            $this->Text(102, $this_sectionY + 32, "SK30 8330 0000 0023 0299 9840");
            $this->SetFont($fontName, "", 9);
            $this->Text(93, $this_sectionY + 36, "SWIFT:");
            $this->SetFont($fontName, "B", 9);
            $this->Text(104, $this_sectionY + 36, "FIOZSKBAXXX");
            $this->Rect(160, $this_sectionY, 40, 41, "F"); #3



            $this_sectionY += 36;
            //Display Table headings
            $this->SetY($this_sectionY + 10);
            $this->SetX(10);
            $this->SetFont($fontName, 'B', 10);
            $this->Cell(10, 9, "Č.", 1, 0, "", true);
            $this->Cell(90, 9, "NÁZOV", 1, 0, "", true);
            $this->Cell(40, 9, "MNOŽSTVO", 1, 0, "", true);
            $this->Cell(30, 9, "JEDN. CENA", 1, 0, "", true);
            $this->Cell(20, 9, "SPOLU", 1, 1, "", true);
            $this->SetFont($fontName, '', 12);
            $this->SetTextColor(0, 0, 0);
            $product_count = 1;
            $this_cell_y = $this->GetY() + 2;
            foreach ($data['products'] as $row) {
                if ($this->GetY() > 250){
                    $this->SetY(0);
                    $this->AddPage('P', 'A4');
                    $this_cell_y = $this->GetY() + 2;
                } 
                //$this_cell_y = 20; 
                $this->SetX(10);
                $this->SetY($this_cell_y);
                $this->Cell(10, 5, $product_count . ". ", "", 0);
                $this_cell_y_height = $this->GetY();
                $this->MultiCell(90, 5, $row["name"], "", 0);
                $this_cell_y_height = $this->GetY() - $this_cell_y_height;
                $this->SetY($this_cell_y);
                $this->SetX(110);
                $this->Cell(40, 5, $row["qty"], "", 0, "C");
                $this->Cell(30, 5, "  " . dot2comma($row["price"] / $row["qty"]), "", 0, "C");
                $this->Cell(20, 5, number_format($row["price"], 2, ",") . "     ", "", 1, "R");
                $product_count++;
                $this_cell_y += $this_cell_y_height + 2;
            }

            $this->SetY($this_cell_y + 10);
            $this->SetX(110);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont($fontName, 'B', 12);
            $this->Cell(20, 9, "SPOLU", 1, 0, "R", true);
            $this->Cell(70, 9, dot2comma($data['price_total']) . " EUR" . "    ", 1, 0, "R", true);
            
            $this->SetY($this->GetY() + 10);
            $this->SetX(110);
            $this->SetFont($fontName, "", 9);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(91, 9, "Pladba GoPay " . dot2comma($data['price_total']) . " EUR", 0, 0, "R");
            $this->SetY($this->GetY() + 5);
            $this->SetX(110);
            $this->Cell(91, 9, "Na úhradu 0 EUR", 0, 0, "R");
        }
        function Footer() {

            global $fontName;
            $this->SetTextColor(0, 0, 0);

            $this->Image(__DIR__ . "/podpis.jpg", 10, 260);


            $this->SetY(-10);
            $this->SetFont($fontName, '', 12);
            $this->Cell(0, 10, "www.heligonkovaakademia.sk", 0, 1, "C");

            $this->SetXY(-20, -7);
            $this->SetFont($fontName, '', 8);
            $this->AliasNbPages('{totalPages}');
            $this->Cell(30, 5, "Strana " . $this->PageNo() . "/{totalPages}");
        }
    }
    //Create A4 Page with Portrait 
    $pdf = new PDF("P", "mm", "A4");
    $pdf->AddPage();
    $pdf->body($inputData);
    $savePath = realpath(__DIR__ . "/../../data/uploads/invoices/") . "/" . $inputData['invoice_no'] . '.pdf';
    $pdf->Output('F', $savePath);
    //$pdf->Output();

    $logData = " errors:".json_encode(error_get_last());
    $logData .= " | " . json_encode($inputData);
    $logPath = __dir__ . "/invoice.log";
    $mode = (!file_exists($logPath)) ? 'w':'a';
    $logfile = fopen($logPath, $mode);
    fwrite($logfile, "\r\n". time() . "generateInvoice.php - " . $logData);
    fclose($logfile);
}



