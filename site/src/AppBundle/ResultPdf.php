<?php

namespace AppBundle;

use fpdf\FPDF;

class ResultPdf extends FPDF
{
    const LINE_HEIGHT = 5;
    const PAGE_WIDTH = 210;
    const PAGE_HEIGHT = 297;
    const MARGIN = 10;
    const CONTENT_WIDTH = 190;//self::PAGE_WIDTH - 2 * self::MARGIN;

    private $logo;

    public function __construct($logo)
    {
        parent::__construct();
        $this->SetFont("Times");
        $this->logo = $logo;
    }

    function Header()
    {
        $pdf = $this;
        $height = self::LINE_HEIGHT;
        $pdf->Image($this->logo, $pdf->getX(), $pdf->getY(), 20, 20);
        $pdf->SetFont("", "B");
        $pdf->Cell(0, $height, "Institut Saint-Dominique - Section secondaire", 0, 1, "C");
        $pdf->SetFont("", "");
        $pdf->MultiCell(0, $height, "Rue Caporal Claes, 38 - 1030 Bruxelles\nTel.: 02/240.14.10 - Fax: 02/240.16.11", 0, "C");
        $email="saintdominique@ens.irisnet.be";
        $site = "www.saintdominique.be";
        $width = $pdf->GetStringWidth("$email - $site");
        $pdf->SetX($pdf->GetX() + (self::PAGE_WIDTH - $pdf->getX() - self::MARGIN - $width)/2);
        $pdf->Write($height, $email, "mailto:$email");
        $pdf->Write($height, " - ");
        $pdf->Write($height, $site, $site);
        $pdf->Ln();
        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ self::CONTENT_WIDTH, $pdf->GetY());
        $pdf->Ln();
    }
}
